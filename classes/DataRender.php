<?php

namespace LexSystems;
class DataRender
{
    public static function render(array $data)
    {
        $output = "<style>
        body{
        background-color: #f2f2f2;
        font-size: 15px;
        font-family: arial;
        }
        ul {
          padding: 0;
          margin: 0;
          list-style-type: none;
          position: relative;
        }
        li {
          border-left: 2px solid #000;
          margin-left: 1em;
          padding-left: 1em;
          position: relative;
        }
        li li {
          margin-left: 0;
        }
        li::before {
          content:'┗';
          color: #000;
          position: absolute;
          top: -5px;
          left: -9px;
        }
        ul > li:last-child {
          border-left: 2px solid transparent;
        }
        </style>";

        $output .= DataRender::renderData($data);

        return $output;
    }


    public static function renderData(array $data)
    {
        $output = "<ul>";
        foreach($data as $key=>$val)
        {
            if(is_array($val))
            {
                $output .= '<ul>';
                $output .= "<li><b>$key</b> : ". DataRender::renderData($val)."</li>";
                $output .= '</ul>';
            }
            else
            {

                $output .= "<li><b>$key</b> - $val</li>";
            }
        }
        $output .= "</ul>";

        return $output;
    }
}