<?php

require('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once('uploadcourselib.php');
require_once('uploadcourse_form.php');

$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);

@set_time_limit(60*60); // 1 hour should be enough
raise_memory_limit(MEMORY_HUGE);

require_login();
admin_externalpage_setup('courseupload');

// TODO: capability check

$returnurl = new moodle_url('/course/uploadcourse.php');

$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

$STD_FIELDS = array('id',
'category',
'sortorder',
'password',
'fullname',
'shortname',
'idnumber',
'summary',
'format',
'showgrades',
'modinfo',
'newsitems',
'teacher',
'teachers',
'student',
'students',
'guest',
'startdate',
'enrolperiod',
'numsections',
'marker',
'maxbytes',
'showreports',
'visible',
'hiddensections',
'groupmode',
'groupmodeforce',
'defaultgroupingid',
'lang',
'theme',
'cost',
'currency',
'timecreated',
'timemodified',
'metacourse',
'requested',
'restrictmodules',
'expirynotify',
'expirythreshold',
'notifystudents',
'enrollable',
'enrolstartdate',
'enrolenddate',
'enrol',
'defaultrole');

if (empty($iid)) {
    $mform1 = new admin_uploadcourse_form1();

    if ($formdata = $mform1->get_data()) {
        $iid = csv_import_reader::get_new_iid('uploadcourse');
        $cir = new csv_import_reader($iid, 'uploadcourse');

        $content = $mform1->get_file_content('coursefile');

        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
        unset($content);

        if ($readcount === false) {
            print_error('csvloaderror', '', $returnurl);
        } else if ($readcount == 0) {
            print_error('csvemptyfile', 'error', $returnurl);
        }
        // test if columns ok
        $filecolumns = cu_validate_course_upload_columns($cir, $STD_FIELDS, $returnurl);
        // continue to form2

    } else {
        echo $OUTPUT->header();

        echo $OUTPUT->heading_with_help(get_string('uploadcourse', 'admin'), 'uploadcourse', 'admin');

        $mform1->display();
        echo $OUTPUT->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploadcourse');
    $filecolumns = cu_validate_course_upload_columns($cir, $STD_FIELDS, $returnurl);
}

$mform2 = new admin_uploadcourse_form2(null, array('columns'=>$filecolumns, 'data'=>array('iid'=>$iid, 'previewrows'=>$previewrows)));

// If a file has been uploaded, then process it
if ($formdata = $mform2->is_cancelled()) {
    $cir->cleanup(true);
    redirect($returnurl);

} else if ($formdata = $mform2->get_data()) {
    // Print the header
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('uploadcourseresult', 'admin'));

    $cir->init();
    $linenum = 1;
    
    // Load course categories
    $categories = get_categories();
    $category_cache = array();
    foreach ($categories as $id => $category) {
        $category_cache[$id] = $category->name;
    }

    while ($line = $cir->next()) {
        
        $course = new stdClass();

        // add fields to user object
        foreach ($line as $keynum => $value) {
	     $key = $filecolumns[$keynum];
	     $course->$key = $value;
	}

	// Category handling should be elegant
	if (is_numeric($course->category)) {
	    if (!array_key_exists($course->category, $categories)) {
	        // Key not found; set to misc
	        $course->category = 1;
	    }
	} else {
	    $items = explode("/", $course->category);
	    $current_category = 0;
	    foreach ($items as $item) {
	        $category_search = array_search($item, $category_cache);
	        if (!$category_search) {
	            // New category; create
	            $data = new stdClass();
	            $data->name = $item;
	            $data->parent = $current_category;
	            $data->sortorder = 999;
	            $data->id = $DB->insert_record('course_categories', $data);
	            $data->context = get_context_instance(CONTEXT_COURSECAT, $data->id);
	            mark_context_dirty($data->context->path);
	            $current_category = $data->id;
	            $category_cache[$data->id] = $data->name;
	        } else {
	            $current_category = $category_search;
	        }
	    }
	    $course->category = $current_category;
	}

	$course->timemodified = $today;
	$course->timecreated = $today;

	create_course($course);

    }

    fix_course_sortorder();

    $cir->close();
    $cir->cleanup(true);

    echo $OUTPUT->continue_button($returnurl);

    echo $OUTPUT->footer();
    die;

}

// Print the header
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('uploadcoursepreview', 'admin'));

// NOTE: this is JUST csv processing preview, we must not prevent import from here if there is something in the file!!
//       this was intended for validation of csv formatting and encoding, not filtering the data!!!!
//       we definitely must not process the whole file!

// preview table data
$data = array();
$cir->init();
$linenum = 1; //column header is first line
while ($linenum <= $previewrows and $fields = $cir->next()) {
    $linenum++;
    $rowcols = array();
    $rowcols['line'] = $linenum;
    foreach($fields as $key => $field) {
        $rowcols[$filecolumns[$key]] = s($field);
    }
    $rowcols['status'] = array();

    if (isset($rowcols['fullname'])) {
        $stdfullname = clean_param($rowcols['fullname'], PARAM_MULTILANG);
        if ($rowcols['fullname'] !== $stdfullname) {
            $rowcols['status'][] = get_string('invalidcoursenameupload');
        }
    } else {
        $rowcols['status'][] = get_string('missingfullname');
    }

    if (isset($rowcols['shortname'])) {
        $stdshortname = clean_param($rowcols['shortname'], PARAM_MULTILANG);
        if ($rowcols['shortname'] !== $stdshortname) {
            $rowcols['status'][] = get_string('invalidcoursenameupload');
        }
    } else {
        $rowcols['status'][] = get_string('missingshortname');
    }


    $rowcols['status'] = implode('<br />', $rowcols['status']);
    $data[] = $rowcols;
}

if ($fields = $cir->next()) {
    $data[] = array_fill(0, count($fields) + 2, '...');
}
$cir->close();

$table = new html_table();
$table->id = "cupreview";
$table->attributes['class'] = 'generaltable';
$table->tablealign = 'center';
$table->summary = get_string('uploadcoursepreview', 'admin');
$table->head = array();
$table->data = $data;

$table->head[] = get_string('uucsvline', 'admin');
foreach ($filecolumns as $column) {
    $table->head[] = $column;
}
$table->head[] = get_string('status');

echo html_writer::tag('div', html_writer::table($table), array('class'=>'flexible-wrap'));
/// Print the form

$mform2->display();
echo $OUTPUT->footer();
die;

?>