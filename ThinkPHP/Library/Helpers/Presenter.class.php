<?php
/**
 * Created by PhpStorm.
 * User: hubeiwei
 * Date: 2016/6/8
 * Time: 16:05
 * To change this template use File | Settings | File Templates.
 */

namespace Helpers;

class Presenter
{
    /**
     * 微信用户-性别
     * 表：t_users_all
     * created by 胡倍玮
     */
    const USER_WX_SEX_UNKNOWN = 0;
    const USER_WX_SEX_MAN = 1;
    const USER_WX_SEX_WOMAN = 2;
    const USER_WX_SEX_SECRECY = 3;
    public static $user_wx_sex_map = [
        self::USER_WX_SEX_UNKNOWN => '未知',
        self::USER_WX_SEX_MAN => '男',
        self::USER_WX_SEX_WOMAN => '女',
        self::USER_WX_SEX_SECRECY => '保密',
    ];

    /**
     * 微信用户-关注状态
     * 表：t_users_all
     * created by 胡倍玮
     */
    const USER_SUBSCRIBE_NO = 0;
    const USER_SUBSCRIBE_YES = 1;
    public static $user_subscribe_map = [
        self::USER_SUBSCRIBE_NO => '未关注',
        self::USER_SUBSCRIBE_YES => '已关注',
    ];

    /**
     * 手机用户-状态
     * 表：t_users_member
     * created by 胡倍玮
     */
    const MEMBER_STATUS_NORMAL = 1;
    const MEMBER_STATUS_LOCKED = 2;
    const MEMBER_STATUS_DELETED = 99;
    public static $member_status_map = [
        self::MEMBER_STATUS_NORMAL => '正常',
        self::MEMBER_STATUS_LOCKED => '锁定',
        self::MEMBER_STATUS_DELETED => '删除',
    ];

    /**
     * 用户-默认地址
     * 表：t_users_address
     * created by 胡倍玮
     */
    const ADDRESS_DEFAULT_YES = 1;
    const ADDRESS_DEFAULT_NO = 2;
    public static $address_default_map = [
        self::ADDRESS_DEFAULT_YES => '默认',
        self::ADDRESS_DEFAULT_NO => '非默认',
    ];

    /**
     * 自动回复-接收消息类型
     * 表：t_city_auto_reply
     * created by 胡倍玮
     */
    const REPLY_MSG_TYPE_TEXT = 'text';
    const REPLY_MSG_TYPE_EVENT = 'event';
    const REPLY_MSG_TYPE_BUTTON = 'button';
    public static $reply_msg_type_map = [
        self::REPLY_MSG_TYPE_TEXT => '消息',
        self::REPLY_MSG_TYPE_EVENT => '事件',
        self::REPLY_MSG_TYPE_BUTTON => '按钮'
    ];

    /**
     * 自动回复-消息回复类型
     * 表：t_city_auto_reply
     * created by 胡倍玮
     */
    const REPLY_TYPE_TEXT = 'text';
    const REPLY_TYPE_IMG = 'img';
    public static $reply_type_map = [
        self::REPLY_TYPE_TEXT => '文本',
        self::REPLY_TYPE_IMG => '图片',
    ];

    /**
     * 自动回复-状态
     * 表：t_city_auto_reply
     * created by 胡倍玮
     */
    const REPLY_STATUS_ENABLE = 1;
    const REPLY_STATUS_DISABLE = 0;
    const REPLY_STATUS_DELETED = 99;
    public static $reply_status_map = [
        self::REPLY_STATUS_ENABLE => '正常',
        self::REPLY_STATUS_DISABLE => '停用',
        self::REPLY_STATUS_DELETED => '已删除',
    ];

    /**
     * 临时素材-素材类型
     * 表：t_city_media
     * created by 胡倍玮
     */
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VOICE = 'voice';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_THUMB = 'thumb';
    public static $media_type_map = [
        self::MEDIA_TYPE_IMAGE => '图片',
        self::MEDIA_TYPE_VOICE => '语音',
//        self::MEDIA_TYPE_VIDEO => '视频',
        self::MEDIA_TYPE_THUMB => '缩略图',
    ];

    /**
     * 微信支付记录-操作类型
     * 表：t_wechat_pay_record
     * created by 胡倍玮
     */
    const WECHAT_PAY_TYPE_CASH = 1;
    public static $wechat_pay_type_map = [
        self::WECHAT_PAY_TYPE_CASH => '提现',
    ];

    /**
     * 刮刮卡-状态
     * 表：t_card
     * created by 胡倍玮
     */
    const CARD_STATUS_ENABLE = 1;
    const CARD_STATUS_DISABLE = 0;
    public static $card_status_map = [
        self::CARD_STATUS_ENABLE => '启用',
        self::CARD_STATUS_DISABLE => '禁用',
    ];

    /**
     * 刮刮卡记录-奖品类型
     * 表：t_card_record
     * created by 胡倍玮
     */
    const CARD_AWARD_TYPE_INTEGRAL = 'mbi';
    const CARD_AWARD_TYPE_GOOD = 'entity';
    const CARD_AWARD_TYPE_MONEY = 'money';
    const CARD_AWARD_TYPE_THANK = 'thank';
    public static $card_award_type_map = [
        self::CARD_AWARD_TYPE_INTEGRAL => 'M币',
        self::CARD_AWARD_TYPE_GOOD => '实物',
        self::CARD_AWARD_TYPE_MONEY => '现金',
        self::CARD_AWARD_TYPE_THANK => '谢谢参与',
    ];

    /**
     * 刮刮卡记录-奖品状态
     * 表：t_card_record
     * created by 胡倍玮
     */
    const CARD_AWARD_STATUS_DISABLE = '0';
    const CARD_AWARD_STATUS_ENABLE = '1';
    const CARD_AWARD_STATUS_DELETE = '2';
    public static $card_award_status_map = [
        self::CARD_AWARD_STATUS_DISABLE => '禁用',
        self::CARD_AWARD_STATUS_ENABLE => '启用',
        self::CARD_AWARD_STATUS_DELETE => '已删除',
    ];

    /**
     * 刮刮卡记录-刮卡状态
     * 表：t_card_record
     * created by 胡倍玮
     */
    const CARD_RECORD_STATUS_CLOSE = 0;
    const CARD_RECORD_STATUS_OPEN = 1;
    const CARD_RECORD_STATUS_UN_CONSIGNMENT = 2;
    const CARD_RECORD_STATUS_CONSIGNMENT = 3;
    const CARD_RECORD_STATUS_SIGNED = 4;
    public static $card_record_status_map = [
        self::CARD_RECORD_STATUS_CLOSE => '未刮',
        self::CARD_RECORD_STATUS_OPEN => '已刮',
        self::CARD_RECORD_STATUS_UN_CONSIGNMENT => '未发货',
        self::CARD_RECORD_STATUS_CONSIGNMENT => '已发货',
        self::CARD_RECORD_STATUS_SIGNED => '已签收',
    ];
}
