//index.js
//获取应用实例
var app = getApp()
Page({
    data: {
        addressList: []
    },

    selectTap: function(e) {
        var id = e.currentTarget.dataset.id;
        wx.request({
            url: 'https://small.kuaiduodian.com/' + '/user/shipping_address_update' + '?mallname=' + app.globalData.subDomain,
            data: {
                token: app.globalData.token,
                id: id,
                isdefault: 'true'
            },
            success: (res) => {
                wx.navigateBack({})
            }
        })
    },

    addAddess: function() {
        wx.navigateTo({
            url: "/pages/address-add/index"
        })
    },

    editAddess: function(e) {
        wx.navigateTo({
            url: "/pages/address-add/index?id=" + e.currentTarget.dataset.id
        })
    },

    onLoad: function() {
        console.log('onLoad')



    },
    onShow: function() {
        this.initShippingAddress();
    },
    initShippingAddress: function() {
        var that = this;
        wx.request({
            url: 'https://small.kuaiduodian.com/' + '/user/shipping_address_list' + '?mallname=' + app.globalData.subDomain,
            data: {
                token: app.globalData.token
            },
            success: (res) => {
                if (res.data.code == 0) {
                    that.setData({
                        addressList: res.data.data
                    });
                }
            }
        })
    }

})