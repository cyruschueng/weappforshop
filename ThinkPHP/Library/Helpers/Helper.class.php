<?php
/**
 * Created by PhpStorm.
 * User: hubeiwei
 * Date: 2016/6/17
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

namespace Helpers;

class Helper
{
    /** 临时素材路径 */
    const MEDIA_PATH = 'wx_media/';

    /**
     * 获取临时素材完整路径
     *
     * @param string $fileName
     * @return string
     */
    public static function getMediaPath($fileName = '')
    {
        return dirname($_SERVER['SCRIPT_FILENAME']) . '/upload/' . self::MEDIA_PATH . $fileName;
    }

    /**
     * For example,
     *
     * ```php
     * $array = [
     *     ['id' => '123', 'name' => 'aaa'],
     *     ['id' => '124', 'name' => 'bbb'],
     *     ['id' => '345', 'name' => 'ccc'],
     * ];
     *
     * $result = ArrayHelper::map($array, 'id', 'name');
     * // the result is:
     * // [
     * //     '123' => 'aaa',
     * //     '124' => 'bbb',
     * //     '345' => 'ccc',
     * // ]
     * ```
     *
     * @param array $data
     * @param string $from
     * @param string $to
     * @return array
     */
    public static function arrayMap($data, $from, $to)
    {
        $map = [];
        foreach ($data as $value) {
            $map[$value[$from]] = $value[$to];
        }
        return $map;
    }
}