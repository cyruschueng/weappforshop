var commonCityData = require('../../utils/city.js')
    //获取应用实例
var app = getApp()




Page({
    data: {
        provinces: [],
        citys: [],
        districts: [],
        selProvince: '请选择',
        selCity: '请选择',
        selDistrict: '请选择',
        selProvinceIndex: 0,
        selCityIndex: 0,
            code: "请输入区/街道",
            flag: 0,
            lat: '',
            lon: '',
        selDistrictIndex: 0
    },
    bindCancel: function() {
        wx.navigateBack({})
    },




//获取地理位置
  chosLoc: function (e) {
    var that = this;
    
    wx.chooseLocation({
      success: function (res) {
        that.data.flag = 1;
        that.data.code = res.address;
        that.data.lat = res.latitude;
        that.data.lon = res.longitude;
        that.setData({
          code: res.address
        })
      },
    })
  },









    bindSave: function(e) {
        var that = this;
        var linkman = e.detail.value.linkman;
        var address = e.detail.value.address;
        var mobile = e.detail.value.mobile;
        var code = e.detail.value.code;
        var province = this.data.selProvince ;
        var city = this.data.selCity;
        var district = this.data.selDistrict;
        if (linkman == "") {
            wx.showModal({
                title: '提示',
                content: '请填写联系人姓名',
                showCancel: false
            })
            return
        }
        if (mobile == "") {
            wx.showModal({
                title: '提示',
                content: '请填写手机号码',
                showCancel: false
            })
            return
        }
        // if (this.data.selProvince == "请选择") {
        //     wx.showModal({
        //         title: '提示',
        //         content: '请选择地区',
        //         showCancel: false
        //     })
        //     return
        // }
        // if (this.data.selCity == "请选择") {
        //     wx.showModal({
        //         title: '提示',
        //         content: '请选择地区',
        //         showCancel: false
        //     })
        //     return
        // }
        // var cityid = commonCityData.cityData[this.data.selProvinceIndex].cityList[this.data.selCityIndex].id;
        // var districtid;
        // if (this.data.selDistrict == "请选择") {
        //     districtid = cityid;
        // } else {
        //     districtid = commonCityData.cityData[this.data.selProvinceIndex].cityList[this.data.selCityIndex].districtList[this.data.selDistrictIndex].id;
        // }

    
        if (address == "") {
            wx.showModal({
                title: '提示',
                content: '请填写详细地址',
                showCancel: false
            })
            return
        }
        if (code == "" || this.data.lat=='' || this.data.lon == '') {
            wx.showModal({
                title: '提示',
                content: '填写地址不能为空!',
                showCancel: false
            })
            return
        }



        //   var distance = getGreatCircleDistance(that.data.shoplat, that.data.shoplon, alladdr[item].lat, alladdr[item].lon)
        // if (distance >= 3000) {//大于3公里超出范围
        //   overaddr.push(alladdr[item])
        // } else {//小于3公里
        //   insideaddr.push(alladdr[item])
        // }
        var apiAddoRuPDATE = "add";
        var apiAddid = that.data.id;
        if (apiAddid) {
            apiAddoRuPDATE = "update";
        } else {
            apiAddid = 0;
        }


        if(app.globalData.token == undefined){
     
        }
        wx.request({
            url: 'https://small.kuaiduodian.com/' + '/user/shipping_address_' + apiAddoRuPDATE + '?mallname=' + app.globalData.subDomain,
            data: {
                token: app.globalData.token,
                id: apiAddid,
                // provinceid: commonCityData.cityData[this.data.selProvinceIndex].id,

                // province: province,
                // city: city,
                // district: district,

                // cityid: cityid,
                // districtid: districtid,
                latitude: this.data.lat,
                longitude: this.data.lon,
                  
                linkman: linkman,
                address: address,
                mobile: mobile,
                code: this.data.code,
                isdefault: 1
            },
            success: function(res) {
                if (res.data.code != 0) {
                    // 登录错误 
                    wx.hideLoading();
                    wx.showModal({
                        title: '失败',
                        content: res.data.msg,
                        showCancel: false
                    })
                    return;
                }
                // 跳转到结算页面
                wx.navigateBack({})
            }
        })
    },
    initCityData: function(level, obj) {
        if (level == 1) {
            var pinkArray = [];
            for (var i = 0; i < commonCityData.cityData.length; i++) {
                pinkArray.push(commonCityData.cityData[i].name);
            }
            this.setData({
                provinces: pinkArray
            });
        } else if (level == 2) {
            var pinkArray = [];
            var dataArray = obj.cityList
            for (var i = 0; i < dataArray.length; i++) {
                pinkArray.push(dataArray[i].name);
            }
            this.setData({
                citys: pinkArray
            });
        } else if (level == 3) {
            var pinkArray = [];
            var dataArray = obj.districtList
            for (var i = 0; i < dataArray.length; i++) {
                pinkArray.push(dataArray[i].name);
            }
            this.setData({
                districts: pinkArray
            });
        }

    },
    bindPickerProvinceChange: function(event) {
        var selIterm = commonCityData.cityData[event.detail.value];
        this.setData({
            selProvince: selIterm.name,
            selProvinceIndex: event.detail.value,
            selCity: '请选择',
            selDistrict: '请选择'
        })
        this.initCityData(2, selIterm)
    },
    bindPickerCityChange: function(event) {
        var selIterm = commonCityData.cityData[this.data.selProvinceIndex].cityList[event.detail.value];
        this.setData({
            selCity: selIterm.name,
            selCityIndex: event.detail.value,
            selDistrict: '请选择'
        })
        this.initCityData(3, selIterm)
    },
    bindPickerChange: function(event) {
        var selIterm = commonCityData.cityData[this.data.selProvinceIndex].cityList[this.data.selCityIndex].districtList[event.detail.value];
        if (selIterm && selIterm.name && event.detail.value) {
            this.setData({
                selDistrict: selIterm.name,
                selDistrictIndex: event.detail.value
            })
        }
    },
    onLoad: function(e) {
        var that = this;
        this.initCityData(1);
        var id = e.id;
        if (id) {
            // 初始化原数据
            wx.showLoading();
            wx.request({
                    url: 'https://small.kuaiduodian.com/' + '/user/shipping_address_detail' + '?mallname=' + app.globalData.subDomain,
                    data: {
                        token: app.globalData.token,
                        id: id
                    },
                    success: function(res) {
                        wx.hideLoading();
                        if (res.data.code == 0) {
                            that.setData({
                                id: id,
                                addressData: res.data.data,

                                            code: res.data.data.code,
            flag: (res.data.data.latitude && res.data.data.longitude) ?0:1,
            lat: res.data.data.latitude,
            lon: res.data.data.longitude,
                                selProvince: commonCityData.cityData,
                               selProvince:res.data.data.province,
                                selCity: res.data.data.city,
                                selDistrict: res.data.data.district
                            });
                            return;
                        } else {
                            wx.showModal({
                                title: '提示',
                                content: '无法获取快递地址数据',
                                showCancel: false
                            })
                        }
                    }
                })
                // 
        }
    },
    selectCity: function() {

    },
    deleteAddress: function(e) {
        var that = this;
        var id = e.currentTarget.dataset.id;
        wx.showModal({
            title: '提示',
            content: '确定要删除该收货地址吗？',
            success: function(res) {
                if (res.confirm) {
                    wx.request({
                        url: 'https://small.kuaiduodian.com/' + '/user/shipping_address_delete' + '?mallname=' + app.globalData.subDomain,
                        data: {
                            token: app.globalData.token,
                            id: id
                        },
                        success: (res) => {
                            wx.navigateBack({})
                        }
                    })
                } else if (res.cancel) {
                    console.log('用户点击取消')
                }
            }
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
