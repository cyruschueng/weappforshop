<?php
/**
 * Created by PhpStorm.
 * User: hubeiwei
 * Date: 2016/6/8
 * Time: 18:21
 * To change this template use File | Settings | File Templates.
 */

namespace Helpers;

class HtmlHelper
{
    /**
     * @param string $attribute 属性名
     * @param array $list 形如[id1 => 'name1', ...]
     * @param string|int|null $value
     * @param array $options html属性
     * @return string
     */
    public static function dropDownList($attribute, $list, $value = null, $options = ['class' => "form-control"])
    {
        $option = '';
        if ($options) {
            foreach ($options as $optionKey => $optionValue) {
                if ($optionKey == 'name') {
                    continue;
                }
                $option .= ' ' . $optionKey . '="' . $optionValue . '"';
            }
        }
        $html = "<select name=\"{$attribute}\" {$option}\">";
        foreach ($list as $k => $v) {
            $selected = $value == $k ? 'selected' : '';
            $html .= "<option value=\"{$k}\" {$selected}>{$v}</option>";
        }
        $html .= '</select>';
        return $html;
    }
}