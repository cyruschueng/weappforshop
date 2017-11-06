//app.js
App({
    onLaunch: function() {
        console.log('test');
        var that = this;
        //  获取商城名称
        wx.request({
            url: 'https://small.kuaiduodian.com/' + '/config/get_value' + '?mallname=' + that.globalData.subDomain,
            data: {
                key: 'mallname'
            },
            success: function(res) {
                wx.setStorageSync('mallname', res.data.data.value);
            }
        })
        this.login();
    },
    login: function() {
        var that = this;
        var token = that.globalData.token;
        if (token) {
            wx.request({
                url: 'https://small.kuaiduodian.com/' + '/user/check_token' + '?mallname=' + that.globalData.subDomain,

                data: {
                    token: token
                },
                success: function(res) {
                    if (res.data.code != 0) {
                        that.globalData.token = null;
                        that.login();


                    }
                }
            })
            return;
        }
        wx.login({
            success: function(res) {
                wx.request({
                    url: 'https://small.kuaiduodian.com/' + '/user/wxapp_login' + '?mallname=' + that.globalData.subDomain,
                    data: {
                        code: res.code
                    },
                    success: function(res) {
                        if (res.data.code == 10000) {
                            // 去注册
                            that.registerUser(res.data.data.openid);
                            return;
                        }
                        if (res.data.code != 0) {

                            console.log(res);
                            // 登录错误 
                            wx.hideLoading();
                            wx.showModal({
                                title: '提示',
                                content: '无法登录，请重试^^',
                                showCancel: false
                            })
                            return;

                        }
                        that.globalData.token = res.data.data.token;
                    }
                })
            }
        })
    },

    registerUser: function(openid) {
        var that = this;
        wx.login({
            success: function(res) {
                var code = res.code; // 微信登录接口返回的 code 参数，下面注册接口需要用到
                wx.getUserInfo({
                    success: function(res) {
                        var iv = res.iv;
                        var encryptedData = res.encryptedData;
                        // 下面开始调用注册接口
                        wx.request({
                            url: 'https://small.kuaiduodian.com/' + '/user/wxapp_registercomplex' + '?mallname=' + that.globalData.subDomain,
                            data: { openid: openid, code: JSON.stringify(res) }, // 设置请求的 参数

                            success: (res) => {
                                wx.hideLoading();
                                that.login();
                            }
                        })
                    }
                })
            }
        })
    },

    globalData: {
        userInfo: null,
 

                           slatitude : 28.20626 ,
   slongitude : 112.9741 ,
        subDomain: "appid"
    }
})