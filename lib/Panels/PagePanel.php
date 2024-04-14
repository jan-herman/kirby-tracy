<?php

namespace JanHerman\Tracy\Panels;

use Tracy\IBarPanel;
use Tracy\Helpers;

class PagePanel implements IBarPanel
{
    public function getTab()
    {
        $page = page();

        if (!$page) {
            return;
        }

        $page_status = $page->status();

        $output = '';

        if ($page_status === 'listed') {
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="rgb(168, 212, 82)"></path></svg>';
        } elseif ($page_status === 'unlisted') {
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 21.9966C6.47715 21.9966 2 17.5194 2 11.9966C2 6.47373 6.47715 1.99658 12 1.99658C17.5228 1.99658 22 6.47373 22 11.9966C22 17.5194 17.5228 21.9966 12 21.9966ZM12 19.9966V3.99658C7.58172 3.99658 4 7.5783 4 11.9966C4 16.4149 7.58172 19.9966 12 19.9966Z" fill="rgb(99, 161, 222)"></path></svg>';
        } elseif ($page_status === 'draft') {
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="rgb(236, 85, 85)"></path></svg>';
        } else {
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"></path></svg>';
        }

        $output .= '<span class="tracy-label">page</span>';

        return $output;
    }

    public function getPanel()
    {
        $kirby = kirby();
        $page = page();

        if (!$page) {
            return;
        }

        $template = $page->template();
        $blueprint = $page->blueprint();
        $fields = $blueprint->fields();

        // scripts & styles
        $output = <<<STR
        <style>
            #tracy-debug .tracy-copy-to-clipboard:hover{
                cursor: copy;
                -webkit-user-select: none;
                user-select: none;
            }
            #tracy-debug .tracy-copy-to-clipboard:hover{
                background: rgba(0, 0, 0, 0.05);
            }
            #tracy-debug .tracy-copy-to-clipboard:active{
                background: rgba(0, 0, 0, 0.1);
                cursor: default;
            }
        </style>
        <script>
            function tracyBlueprintPanel() {
                const copyableElements = document.querySelectorAll('#tracy-debug .tracy-copy-to-clipboard');

                copyableElements.forEach(function(cell) {
                    cell.addEventListener('click', async function() {
                        const isCommandKeyHeld = event.metaKey || event.ctrlKey;
                        const isShiftKeyHeld = event.shiftKey;

                        const fieldName = this.textContent.trim();
                        const objectName = this.dataset.objectName || 'page';
                        let text = fieldName;

                        if (isCommandKeyHeld && isShiftKeyHeld) {
                            text = 'n:if="$' + objectName + '->' + fieldName + '()->isNotEmpty()"';
                        } else if (isCommandKeyHeld) {
                            text = '$' + objectName + '->' + fieldName + '()';
                        }

                        navigator.clipboard.writeText(text);
                    });
                });
            }

            tracyBlueprintPanel();
        </script>
        STR;

        // header
        $output .= '<h1>Page</h1>';
        $output .= '<div class="tracy-inner"><div class="tracy-inner-container">';

        // files
        $output .= '<table>';
        $output .= '<tr><td>Blueprint</td><td>' . Helpers::editorLink($kirby->root('blueprints') . '/' . $blueprint->name() . '.yml') . '</td></tr>';
        $output .= '<tr><td>Model</td><td>' . Helpers::editorLink($kirby->root('models') . '/' . $template->name() . '.php') . '</td></tr>';
        $output .= '<tr><td>Template</td><td>' . Helpers::editorLink($template->file()) . '</td></tr>';
        $output .= '<tr><td>Controller</td><td>' . Helpers::editorLink($kirby->root('controllers') . '/' . $template->name() . '.php') . '</td></tr>';
        $output .= '</table>';

        // fields
        $output .= '<table>';

        // table header
        $output .= '<thead><tr>';
        $output .= '<th>Field name</th>';
        $output .= '<th>Type</th>';
        $output .= '<th>Value</th>';
        $output .= '</tr></thead><tbody>';

        // deafult fields
        $output .= '<tr><td class="tracy-copy-to-clipboard">title</td><td>-</td><td>' . $page->title() . '</td></tr>';
        $output .= '<tr><td class="tracy-copy-to-clipboard">slug</td><td>-</td><td>' . $page->slug() . '</td></tr>';
        $output .= '<tr><td class="tracy-copy-to-clipboard">url</td><td>-</td><td>' . $page->url() . '</td></tr>';

        // custom fields
        if ($fields) {
            foreach ($fields as $field) {

                if (in_array($field['type'], ['headline', 'gap', 'line', 'info'])) {
                    continue;
                }

                $field_object = $page->{$field['name']}();

                // table row
                $output .= '<tr>';

                // field name
                $output .= '<td class="tracy-copy-to-clipboard">' . $field['name'] . '</td>';

                // field type
                $output .= '<td><a href="https://getkirby.com/docs/reference/panel/fields/' . $field['type'] . '" target="_blank" title="Docs">' . $field['type'] . '</a></td>';

                // field value
                if ($field_object instanceof \Kirby\Content\Field) {
                    if ($field_object->isNotEmpty()) {
                        if ($field['type'] === 'structure' || $field['type'] === 'object' || $field['type'] === 'button') {
                            $data = [];

                            if ($field['type'] === 'structure') {
                                $data = $field_object->toStructure()->first()->content()->toArray();
                            } else {
                                $data = $field_object->yaml();
                            }

                            $output .= '<td style="padding: 0;"><table style="width: calc(100% + 2px); margin: -1px;">';

                            foreach ($data as $key => $value) {
                                $output .= '<tr>';
                                $output .= '<td class="tracy-copy-to-clipboard" data-object-name="' . $field['name'] . '" style="width: 33.33%;">' . $key . '</td>';
                                $output .= '<td style="width: 66.66%;">' . (is_array($value) ? '- ' . implode('<br>- ', $value) : $value) . '</td>';
                                $output .= '</tr>';
                            }

                            if ($field['type'] === 'structure') {
                                $output .= '<tr><td colspan="2">...</td></tr>';
                            }

                            $output .= '</table></td>';
                        } else {
                            $output .= '<td>' . $field_object->escape()->short(160) . '</td>';
                        }
                    } else { // empty field
                        $output .= '<td></td>';
                    }
                } elseif (is_string($field_object)) { // hidden field outputs string
                    $output .= '<td>' . $field_object . '</td>';
                }

                $output .= '</tr>';
            }
        }

        $output .= '</tbody></table>';

        $output .= '</div></div>';

        return $output;
    }
}
