<?php

class Template
{
    /**
     * @var string $_template The HTML template.
     * @access private
     */
    var $_template = "";

    /**
     * @var array $_template_data Data to be put into the template.
     * @access private
     */
    var $_template_data = array();

    /**
     * @return void
     * @param variable mixed
     * @param value string
     * @desc Assign data to a variable.
     * @access private
     */
    function _template_assign($variable, $value = "")
    {
		if (is_array($variable)) $this->data = $variable + $this->data;
		else $this->_template_data[$variable] = $value;
    }

    /**
     * @return void
     * @param variable string
     * @param value mixed
     * @desc Append data to a variable already assigned.
     * @access private
     */
    function _template_append($variable, $value)
    {
        if (is_array($value)) $this->_template_data[$variable][]  =  $value;
        else $this->_template_data[$variable] .= $value;
    }

    /**
     * @return string
     * @param data array
     * @desc Get the HTML output.
     * @access private
     */
    function _template_toHTML($data = array())
    {
        if ($data) $this->_template_data = $data;
        $html = $this->_template_replace($this->_template, $this->_template_data);

        // Remove any remaining block and conditional statement.
        $html = preg_replace('/<!--\s*BEGIN\s+(\w+)\s*-->.*?<!--\s*END\s+\1\s*-->/is', '', $html);
        $html = preg_replace('/<!--\s*IF\s+(\w+)\s*-->.*?<!--\s*ELSE[^-]*-->(.*?)<!--\s*ENDIF\s+\1\s*-->/is', '\2', $html);
        $html = preg_replace('/<!--\s*IF\s+(\w+)\s*-->.*?<!--\s*ENDIF\s+\1\s*-->/is', '', $html);

        // Remove any remaining {variable}.
        $html = preg_replace('/\{\w+\}/', '', $html);

        return $html;
    }

    /**
     * @return void
     * @param data array
     * @desc Echo the HTML output to the browser.
     * @access private
     */
    function _template_display($data = array())
    {
        echo $this->_template_toHTML($data);
    }

    /**
     * @return string
     * @param template string
     * @param data array
     * @desc Put any data into the template.
     * @access private
     */
    function _template_replace($template, $data)
    {
        foreach ($data as $variable => $value)
        {
            // Process any conditional statement if the variable is true.
            if ($value)
            {
                $template = preg_replace('/<!--\s*IF\s+' . $variable . '\s*-->(.*?)<!--\s*ELSE[^-]*-->.*?<!--\s*ENDIF\s+' . $variable . '\s*-->/is', '\1', $template);
                $template = preg_replace('/<!--\s*IF\s+' . $variable . '\s*-->(.*?)<!--\s*ENDIF\s+' . $variable . '\s*-->/is', '\1', $template);
            }
            // Process arrays only if there is a corresponding template block.
            if (is_array($value) &&
                preg_match('/<!--\s*BEGIN\s+' . $variable . '\s*-->(.*?)<!--\s*END\s+' . $variable . '\s*-->/is', $template, $matches))
            {
                $subValue = "";
                foreach ($value as $subData)
                {
                    // Recursion.
                    $subValue .= $this->_template_replace($matches[1], $subData);
                }
                $template = str_replace($matches[0], $subValue, $template);
            }
            // Common types are: string, int, float. Arrays will be imploded to a string.
            else
            {
                if (is_array($value)) $value = implode("", $value);
                $template = str_replace("{" . $variable . "}", $value, $template);
            }
        }
        return $template;
    }
}

?>