<?php

class xml
{

    const root_node = 'objects';
    const child_root_node = 'object';

    public $xml;
    public $xml_root;
    public $xml_child;
    public $xml_cache_wrapnode;

    private $xml_cache_index = 0;

    /**
     * Возвращает пару ключ-значение (либо только значение) в CDATA представлении
     */
    private function returnCDATA($value, $key = false, $attributes = false)
    {
        if ($key) {
            $childNode = $this->xml->createElement($key);
            if (is_array($attributes)) {
                $childNode = $this->addAttributes($attributes, $childNode);
            }
            $cdata = $this->xml->createCDATASection($value);
            $childNode->appendChild($cdata);
            return $childNode;
        } else {
            return $this->xml->createCDATASection($value);
        }
    }

    /**
     * Возвращает пару ключ-значение (либо только значение) в текстовом представлении
     */
    private function returnKeyValue($value, $key = false, $attributes = false)
    {

        if ($key) {
            $childNode = $this->xml->createElement($key);
            if (is_array($attributes)) {
                $childNode = $this->addAttributes($attributes, $childNode);
            }
            $valueNode = $this->xml->createTextNode($value);
            $childNode->appendChild($valueNode);
            return $childNode;
        } else {
            return $this->xml->createTextNode($value);
        }
    }

    private function addAttributes($array, $node)
    {
        if (is_array($array[$this->xml_cache_index]) && isset($array[$this->xml_cache_index])) {
            foreach ($array[$this->xml_cache_index] as $keyA => $valA) {
                if (is_numeric($keyA)) {
                    throw new Exception('Параметр Атрибут должен быть массивом, где ключ - имя атрибута. ["name"=>"Juan"].');
                }
                $attribute = $this->xml->createAttribute($keyA);
                $attribute->value = $valA;
                $node->appendChild($attribute);
            }

        }
        return $node;
    }

    /**
     * Добавляет узел к дочернему узлу.
     * Если передается массив, то обязательно должен быть передан узел обертка $wrapperForAllChild
     *
     */
    public function addNode($nodeRootName, $nodeValue, $wrapperForAllChild = false, $CDATA = false, $nodeRootAttributes = false, $wrapperAttribute = false, $valueAttribute = false)
    {
        try {
            $node = $this->xml->createElement($nodeRootName);

            if (is_array($nodeRootAttributes))
                $node = $this->addAttributes($nodeRootAttributes, $node);

            if (is_array($nodeValue)) {
                foreach ($nodeValue as $k => $v) {
                    if (is_numeric($k)) {
                        if ($CDATA) {
                            $value = $this->xml->createCDATASection($v);
                        } else {
                            $value = $this->xml->createTextNode($v);
                        }
                        if ($wrapperForAllChild) {
                            $childNode = $this->xml->createElement($wrapperForAllChild);
                            $childNode->appendChild($value);
                            $node->appendChild($childNode);
                        } else {
                            throw new Exception('Необходимо указать обертывающий узел т.к. невозможно создать несколько записей подряд для одного и того же узла ' . $nodeRootName);
                        }
                    } else {
                        if ($CDATA) {
                            $value = $this->returnCDATA($v, $k, $valueAttribute);
                        } else {
                            $value = $this->returnKeyValue($v, $k, $valueAttribute);
                        }
                        if ($wrapperForAllChild) {
                            $childNode = $this->xml->createElement($wrapperForAllChild);
                            if ($wrapperAttribute)
                                $childNode = $this->addAttributes($wrapperAttribute, $childNode);
                            $childNode->appendChild($value);
                            $node->appendChild($childNode);
                        } else {
                            $node->appendChild($value);
                        }

                    }
                    $this->xml_cache_index++;
                }
            } else {
                if ($CDATA) {
                    $value = $this->returnCDATA($nodeValue);
                } else {
                    $value = $this->xml->createTextNode($nodeValue);
                }

                if ($wrapperForAllChild) {
                    $childNode = $this->xml->createElement($wrapperForAllChild);
                    $childNode = $this->addAttributes($nodeRootAttributes, $childNode);
                    $childNode->appendChild($value);
                    $node->appendChild($childNode);
                } else {
                    $node->appendChild($value);
                }
            }
            $this->xml_child->appendChild($node);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Создает документ
     */
    public function createDocument($version = '1.0', $encode = 'UTF-8', $output = true)
    {
        $this->xml = new \DOMDocument($version, $encode);
        $this->xml->formatOutput = $output;
    }

    /**
     * Root обертка xml документа
     */
    public function wrap($wrapName = false)
    {
        if (!$wrapName) $wrap = self::root_node; else $wrap = $wrapName;
        $this->xml_root = $this->xml->createElement($wrap);
    }

    /**
     * Метод запоминает название последнего переданнаго узла для обертки
     * и использует его в дальнейшем, пока не будет объявлен новый узел.
     */
    public function contentWrapBegin($wrapName = false)
    {
        if (!$wrapName) {
            if ($this->xml_cache_wrapnode) {
                $wrap = $this->xml_cache_wrapnode;
            } else {
                $wrap = self::child_root_node;
            }
        } else {
            $wrap = $wrapName;
            $this->xml_cache_wrapnode = $wrap;
        }
        $this->xml_child = $this->xml->createElement($wrap);
    }


    /**
     *  Завершает формирование узла "потомка"
     */
    public function contentWrapEnd()
    {
        $this->xml_root->appendChild($this->xml_child);
        $this->xml_cache_index = 0;
        $this->xml_child = '';
    }

    /**
     * Сохраняет документ
     */
    public function saveDocument($filepath = false)
    {
        $this->xml->appendChild($this->xml_root);
        if (!$filepath) $filepath = 'xml_file_' . time() . '.xml';
        $this->xml->save($filepath);
        die('Done');
    }

    public function validateXML($path)
    {
        libxml_use_internal_errors(TRUE);
        $dom = new DOMDocument();
        $dom->load($path);
        if (!libxml_get_errors()) {
            return true;
        } else {
            return false;
        }
    }

    public function showXML($path)
    {
        $doc = simplexml_load_file($path);
        header("Content-type: text/xml; charset=utf-8");
        return $doc->saveXML();
    }
}

