<?php


namespace Flayfl\LaravelXSLT\Engines;


/**
 * Class ExtendedDomDocument
 * @package Flayfl\LaravelXSLT\Engines
 */
class ExtendedDomDocument
{
    function __construct($node = 'data')
    {
        $this->doc = new \DOMDocument();
        $data = $this->doc->createElement($node);
        $this->doc->appendChild($data);
    }

    public function getDoc()
    {
        return $this->doc;
    }

    public function add($path, $string)
    {
        $fragment = $this->doc->createDocumentFragment();
        $text = $this->doc->createTextNode($string);
        $fragment->appendChild($text);
        $this->out($path,$fragment);
    }

    public function addEscaped($path, $string)
    {
        $string=self::xmlEscape($string);
        $this->add($path, $string);
    }

    public function addXml($path, $string)
    {
        if (!empty($string)) {
            $fragment = $this->doc->createDocumentFragment();
            $fragment->appendXml($string);
            $this->out($path,$fragment);
        }
    }

    public function addArray($path, $array,$name='')
    {
        if (!empty($array)) {
            $xml=self::data_to_xml($array,$name);
            $this->addXml($path,$xml);
        }
    }

    private function out($path,$fragment)
    {
        $path = "/data/$path";
        $path = preg_replace('/\/$/', '', $path);
        $this->createPath($path);
        $xpath = new \DOMXpath($this->doc);
        @$xpath->query($path)->item(0)->appendChild($fragment);
    }

    protected function addErrors()
    {
        if (count($this->errors) > 0) {
            $errors = $this->doc->createElement("errors");
            foreach ($this->errors as $name => $message) {
                $error = $this->doc->createElement($name);
                $error->appendChild($this->doc->createTextNode($message));
                $errors->appendChild($error);
            }
            $this->doc->appendChild($errors);
        }
    }

    static function xmlEscape($string){
        return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
    }

    static function data_to_xml($data,$name=''){
        $out = '';
        if(is_object($data) && method_exists($data, "to_xml")){
            $out.=$data->to_xml();
        }else if(is_array($data)){
            if(count(array_filter(array_keys($data), 'is_string'))){
                if(empty($name)){
                    foreach($data as $item_name => $d){
                        $out.=self::data_to_xml($d,$item_name);
                    }
                }else{
                    $out .= "<$name>";
                    foreach($data as $item_name => $d){
                        $out .=self::data_to_xml($d,$item_name);
                    }
                    $out .= "</$name>";

                }
            }else{
                $out .= "<$name>";
                $name1=substr($name,0,-1);
                foreach($data as $d){
                    $out .=self::data_to_xml($d,$name1);
                }
                $out .= "</$name>";
            }
        }else{
            $out .= "<$name><![CDATA[$data]]></$name>";
        }
        return $out;
    }

    private function createPath($path)
    {
        $xpath = new \DOMXpath($this->doc);
        $elements = $xpath->query($path);
        $missing_element_names = array();
        while ($elements->length == 0 && preg_match('/^(.*)\/([^\/]+)$/', $path, $matches)) {
            array_unshift($missing_element_names, $matches[2]);
            $path = $matches[1];
            $elements = $xpath->query($path);
        }
        foreach ($elements as $element) {
            $current = $element;
            foreach ($missing_element_names as $missing_element_name) {
                $attributes = array();
                $children = array();
                if (preg_match('/^([^\[]+)\[(.*)\]$/', $missing_element_name, $matches)) {
                    $missing_element_name = $matches[1];
                    foreach (preg_split('/\s+and\s+/', $matches[2]) as $pair) {
                        list($name, $value) = preg_split('/\s*=\s*/', $pair);
                        $value = preg_replace("/(^['\"]|['\"]$)/", '', $value);
                        if (preg_match('/^@(.*)$/', $name, $matches)) {
                            $attributes[$matches[1]] = $value;
                        } else {
                            $children[$name] = $value;
                        }
                    }
                }
                $new_element = $this->doc->createElement($missing_element_name);
                foreach ($attributes as $name => $value) {
                    $new_element->setAttribute($name, $value);
                }
                foreach ($children as $name => $value) {
                    $child = $this->doc->createElement($name);
                    $child->appendChild($this->doc->createTextNode($value));
                    $new_element->appendChild($child);
                }
                $current->appendChild($new_element);
                $current = $new_element;
            }
        }
    }

}