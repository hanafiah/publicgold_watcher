<?php

/**
  The MIT License (MIT)

  Copyright (c) 2013 ibnuyahya@gmail.com

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
 */
/**
 * Description of scrapper
 *
 * @author ibnuyahya
 */
require_once('simple_html_dom.php');

class scrapper {

    private $_html;
    private $_trs;

    public function __construct($html = "http://publicgold.com.my/v1/")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $html);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $this->_html = curl_exec($ch);

        curl_close($ch);
    }

    public function showContent()
    {
        return $this->_html;
    }

    public function fetchData()
    {
        $dom = new simple_html_dom();
        $dom->load($this->_html);

        $this->_trs = $dom->find('table', 1)->find('tr');

        $last_update = trim($this->_trs[0]->find('td', 0)->plaintext);
        $last_update = str_replace('(Last updated ', '', $last_update);
        $last_update = str_replace(')', '', $last_update);
        $last_update = trim(str_replace('_', ' ', $last_update));

        //gold bars
        $data = array();
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(3));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(4));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(5));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(6));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(7));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(8));

        //dinars
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(10));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(11));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(12));
        $data[] = array_merge(array('last_update' => $last_update), $this->getData(13));

        return $data;
    }

    private function getData($tr = 0)
    {
        return array(
            'item' => (string) $this->_trs[$tr]->find('td', 0)->plaintext,
            'sell' => preg_replace('/[^0-9.]/s', '', (string) $this->_trs[$tr]->find('td', 1)->plaintext),
            'buy' => preg_replace('/[^0-9.]/s', '', (string) $this->_trs[$tr]->find('td', 2)->plaintext),
        );
    }

}

?>
