<?php
namespace local_teacherassistant\hook\output;

use core\hook\output\before_footer_html_generation;
use html_writer;

class inject_chat_container {

    public static function callback(before_footer_html_generation $hook): void {
        global $PAGE;

        if ($PAGE->context?->contextlevel === CONTEXT_COURSE) {
            echo html_writer::div(
                '',
                'local-chatbot-footer',
                ['id' => 'teacherassistant-chat-container']
            );

            $PAGE->requires->js_call_amd(
            'local_teacherassistant/popup',
            'init', [$COURSE->id]
        );
        }
    }
}
