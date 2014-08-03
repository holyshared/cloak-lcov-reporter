<?php

/**
 * This file is part of cloak.
 *
 * (c) Noritaka Horio <holy.shared.design@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace cloak\report;

use cloak\Result;
use cloak\result\File;
use cloak\result\Line;


/**
 * Class LcovReport
 * @package cloak\report
 */
class LcovReport implements FileSavableReportInterface
{

    /**
     * @var \cloak\Result
     */
    private $result;

    /**
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Save the report to a file
     *
     * @param string $path report file name
     */
    public function saveAs($path)
    {
    }

    public function output()
    {
        echo $this;
    }

    public function __toString()
    {
        $files = $this->result->getFiles();

        $reports = $files->map(function(File $file) {
            $output  = "";
            $output .= "SF:" . $file->getPath() . PHP_EOL;

            $lines = $file->getLines();
            $lineReports = $lines->map(function(Line $line) {

                $output  = "";

                if ($line->isExecuted()) {
                    $output .= "DA:" . $line->getLineNumber() . ",1";
                }

                return $output;
            });

            $elements = $lineReports->all();
            foreach ($elements as $key => $element) {
                if (empty($element)) {
                    unset($elements[$key]);
                }
            }

            $output .= implode(PHP_EOL, $elements) . PHP_EOL;

            $output .= "end_of_record";

            return $output;
        });

        $content = implode(PHP_EOL, $reports->all()) . PHP_EOL;

        return $content;
    }

}
