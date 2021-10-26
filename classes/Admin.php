<?php
namespace LexSystems;

class Admin
{
    /**
     * @param $url
     */
    public static function redirect($url)
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
        } else {
            $content = '<script type="text/javascript">';
            $content .= 'window.location.href="' . $url . '";';
            $content .= '</script>';
            $content .= '<noscript>';
            $content .= '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
            $content .= '</noscript>';
            echo $content;
        }
    }

    /**
     * @param string $string
     * @return array|string|string[]|null
     */
    public function readableVals(string $string)
    {
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', ucfirst($string));
    }

    public function build_html_table(array $array, $style = "horizontal", string $additionalClasses = "")
    {
        if (is_array($array) && count($array) > 0) {
            if ($style == "inline") {

                $table = '<table class="table table-bordered table-hove '.$additionalClasses.'" style="font-size:13px;">';
                // header row
                $table .= '<thead class="bg-primary text-white">';
                $table .= '<tr>';
                foreach($array[0] as $key=>$value){
                    $table .= '<th>' . htmlspecialchars($this->readableVals($key)) . '</th>';
                }
                $table .= '</tr>';
                $table .= '</thead>';

                // data rows
                foreach( $array as $key=>$value){
                    $table .= '<tr>';
                    foreach($value as $key2=>$value2){
                        $table .= '<td>' . htmlspecialchars($value2) . '</td>';
                    }
                    $table .= '</tr>';
                }

                // finish table and return it

                $table .= '</table>';

            } else {
                $table = '<table class="table table-bordered table-hover '.$additionalClasses.'" style="font-size:13px;">';
                $table .= "<tbody>";
                foreach ($array as $array_key => $array_value) {
                    if (!is_array($array_value)) {
                        $table .= "<tr><td><b>".$this->readableVals($array_key)."</b></td><td>$array_value</td></tr>";
                    } else {
                        $table .= "<tr><td>";
                        $table .= $this->build_html_table($array_value);
                        $table .= "</td></tr>";
                    }


                }
                $table .= "</tbody>";
                $table .= "</table>";
            }


            return $table;
        } else {
            return "No data found!";
        }
    }

    /**
     * @return array|null
     */

    public function getLogs(string $limit = "10")
    {
       $logs = new SystemLogger();
       return $logs->getLogEntries($limit);
    }


    /**
     * @return mixed|string
     */
    /**
     * @return mixed|string
     */
    public function getLogsCount()
    {
        $logs = new SystemLogger();
        return $logs->getLogCount();
    }
}