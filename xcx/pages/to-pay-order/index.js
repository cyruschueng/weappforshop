//index.js
//获取应用实例
var app = getApp()

Page({
    data: {
        mallname: wx.getStorageSync('mallname'),
        goodsList: [],
        isNeedLogistics: 0, // 是否需要物流信息
        allGoodsPrice: 0,
        yunPrice: 0,
          isbeyond: 0,
            lat: '',
            lon: '',
        goodsjsonstr: ""
    },
    onShow: function() {
        this.initShippingAddress();
    },
    onLoad: function(e) {
        var that = this;
        var shopList = [];
        var shopListall = [];
        var shopCarInfoMem = wx.getStorageSync('shopCarInfo');
        if (shopCarInfoMem && shopCarInfoMem.shopList) {
            shopList = shopCarInfoMem.shopList
        }
        var isNeedLogistics = 0;
        var allGoodsPrice = 0;

        var goodsjsonstr = "[";
        for (var i = 0,j=0; i < shopList.length; i++) {
            var carShopBean = shopList[i];
            if (carShopBean.active == false) {

                continue;
            }
         
            shopListall.push(carShopBean);
            console.log(carShopBean);
            if (carShopBean.logisticsType > 0) {
                isNeedLogistics = 1;
            }
            allGoodsPrice += carShopBean.price * carShopBean.number

            var goodsJsonStrTmp = '';
            if (j > 0) {
                goodsJsonStrTmp = ",";
            }
            goodsJsonStrTmp += '{"goodsId":' + carShopBean.goodsId + ',"number":' + carShopBean.number + ',"propertyChildIds":"' + carShopBean.propertyChildIds + '","logisticsType":' + carShopBean.logisticsType + '}';
            goodsjsonstr += goodsJsonStrTmp;

            j++;
        }
        goodsjsonstr += "]";
        that.setData({
            goodsList: shopListall,
            isNeedLogistics: isNeedLogistics,
            allGoodsPrice: allGoodsPrice.toFixed(2),
            goodsjsonstr: goodsjsonstr
        });

    },
    createOrder: function(e) {
        wx.showLoading();
        var that = this;
        var loginToken = app.globalData.token // 用户登录 token
        var remark = e.detail.value.remark; // 备注信息

        var postData = {
            token: loginToken,
            goodsjsonstr: that.data.goodsjsonstr,
            remark: remark
        };
        postData.aaaa = 1234;
        if (that.data.isNeedLogistics > 0) {
            if (!that.data.curAddressData) {
                wx.hideLoading();
                wx.showModal({
                    title: '错误',
                    content: '请选择您的收货地址',
                    showCancel: false
                })
                return;
            }

            postData.provinceid = that.data.curAddressData.provinceid;
            postData.cityid = that.data.curAddressData.cityid;
            if (that.data.curAddressData.districtid) {
                postData.districtid = that.data.curAddressData.districtid;
            }
            postData.address = that.data.curAddressData.address;
            postData.linkman = that.data.curAddressData.linkman;
            postData.mobile = that.data.curAddressData.mobile;
            postData.code = that.data.curAddressData.code;
            postData.sumprice = that.data.allGoodsPrice;
            postData.yunprice = that.data.yunPrice;

        }


        wx.request({
            url: 'https://small.kuaiduodian.com/' + '/order/create' + '?mallname=' + app.globalData.subDomain,
            method: 'POST',
            header: {
                'content-type': 'application/x-www-form-urlencoded'
            },
            data: postData, // 设置请求的 参数
            success: (res) => {
                wx.hideLoading();
                console.log(res.data);
                if (res.data.code != 0) {
                    wx.showModal({
                        title: '错误',
                        content: res.data.msg,
                        showCancel: false
                    })
                    return;
                }
              var shopList =[];
                var shopListallnotbuy = [];
                var shopCarInfoMem = wx.getStorageSync('shopCarInfo');
                if (shopCarInfoMem && shopCarInfoMem.shopList) {
                    shopList = shopCarInfoMem.shopList
                }



                for (var i = 0; i < shopList.length; i++) {
                    var carShopBean = shopList[i];
                    if (carShopBean.active == false) {
                        shopListallnotbuy.push(carShopBean);
                        continue;
                    }

                }
                var shopCarInfo = {};
                var tempNumber = 0;
                shopCarInfo.shopList = shopListallnotbuy;
                for (var i = 0; i < shopListallnotbuy.length; i++) {
                    tempNumber = tempNumber + shopListallnotbuy[i].number
                }
                shopCarInfo.shopNum = tempNumber;





                // 清空购物车数据
                wx.removeStorageSync('shopCarInfo');
              if (0<tempNumber){
                wx.setStorageSync(
                 "shopCarInfo", shopCarInfo
                )
              }
                // 下单成功，跳转到订单管理界面
                wx.reLaunch({
                    url: "/pages/order-list/index"
                });
            }
        })
    },
    initShippingAddress: function() {
        var that = this;
        wx.request({
            url: 'https://small.kuaiduodian.com/' + '/user/shipping_address_default' + '?mallname=' + app.globalData.subDomain,
            data: {
                token: app.globalData.token
            },
            success: (res) => {
                console.log(res.data)
                if (res.data.code == 0) {

                    
    that.setData({
                        curAddressData: res.data.data,
                        isbeyond:1,
                        yunPrice:0,
                        sumGoodsPrice:that.data.allGoodsPrice
                    });

         that.data.lat = res.data.data.latitude  ;
   that.data.lon   = res.data.data.longitude  ;

console.log(res.data.data);


    var distance = getGreatCircleDistance(app.globalData.slatitude, app.globalData.slongitude,res.data.data.latitude, res.data.data.longitude)
    

console.log(distance);
        if (res.data.data.longitude  && res.data.data.latitude  && distance >= 3000) {//大于3公里超出范围



    that.setData({
                        curAddressData: res.data.data,
                        isbeyond:1,
                        yunPrice:0,
                        sumGoodsPrice:that.data.allGoodsPrice
                    });
        } else {//小于3公里

var yunp = 0;

if(distance >= 2000)   {
yunp = 8.00;
}else{
    yunp = 5.00;
}

           that.setData({
                isbeyond:0,
                        curAddressData: res.data.data,
                        yunPrice: yunp.toFixed(2),
sumGoodsPrice: (( (yunp.toFixed(2) * 100 ) +  (that.data.allGoodsPrice* 100 ))/100 ).toFixed(2),
                        
                    });
        }
// if(!that.data.lat || !that.data.lat){
//       wx.showModal({
//                         title: '错误',
//                         content: '请重新定位, 获取经纬度来算配送费',
//                         showCancel: false
//                     })
//                     return;

// }

                 
                }
            }
        })
    },
    addAddress: function() {
        wx.navigateTo({
            url: "/pages/address-add/index"
        })
    },
    selectAddress: function() {
        wx.navigateTo({
            url: "/pages/select-address/index"
        })
    }
})


/**  计算两点间的距离*/
/************************** */
// 计算两点之间的距离
var EARTH_RADIUS = 6378137.0;    //单位M(米)
var PI = Math.PI;

function getRad(d) {
  return d * PI / 180.0;
}
//公式1 误差位置（安卓机完好,苹果手机完好）
function getGreatCircleDistance(lat1, lng1, lat2, lng2) {
    // lat1 = lat1*1000000;
    //     lng1 = lng1*1000000;
    //         lat2 = lat2*1000000;
    //             lng2 = lng2*1000000;
  var radLat1 = getRad(lat1);
  var radLat2 = getRad(lat2);

  var a = radLat1 - radLat2;
  var b = getRad(lng1) - getRad(lng2);

  var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
  s = s * EARTH_RADIUS;
  s = Math.round(s * 10000) / 10000.0;

  return s;
}
//公式2详细位置（苹果机测试无压力，安卓机报废）
function getFlatternDistance(lat1, lng1, lat2, lng2) {
  var f = getRad((lat1 + lat2) / 2);
  var g = getRad((lat1 - lat2) / 2);
  var l = getRad((lng1 - lng2) / 2);

  var sg = Math.sin(g);
  var sl = Math.sin(l);
  var sf = Math.sin(f);

  var s, c, w, r, d, h1, h2;
  var a = EARTH_RADIUS;
  var fl = 1 / 298.257;

  sg = sg * sg;
  sl = sl * sl;
  sf = sf * sf;

  s = sg * (1 - sl) + (1 - sf) * sl;
  c = (1 - sg) * (1 - sl) + sf * sl;

  w = Math.atan(Math.sqrt(s / c));
  r = Math.sqrt(s * c) / w;
  d = 2 * w * a;
  h1 = (3 * r - 1) / 2 / c;
  h2 = (3 * r + 1) / 2 / s;

  return d * (1 + fl * (h1 * sf * (1 - sg) - h2 * (1 - sf) * sg));
}
    /************************** */
