var app = getApp();
Page({
    data: {
        orderId: 0,
        goodsList: [
        ],
        yunPrice: "10.00"
    },
    onLoad: function(e) {
        var orderId = e.id;
        this.data.orderId = orderId;
        this.setData({
            orderId: orderId
        });
    },
    onShow: function() {
        var that = this;
        wx.request({
            url: 'https://small.kuaiduodian.com/' + '/order/detail' + '?mallname=' + app.globalData.subDomain,
            data: {
                token: app.globalData.token,
                id: that.data.orderId
            },
            success: (res) => {
                wx.hideLoading();
                if (res.data.code != 0) {
                    wx.showModal({
                        title: '错误',
                        content: res.data.msg,
                        showCancel: false
                    })
                    return;
                }
                that.setData({
                    orderDetail: res.data.data
                });
            }
        })
        var yunPrice = parseFloat(this.data.yunPrice);
        var allprice = 0;
        var goodsList = this.data.goodsList;
        for (var i = 0; i < goodsList.length; i++) {
            allprice += parseFloat(goodsList[0].price) * goodsList[0].number;
        }
        this.setData({
            allGoodsPrice: allprice,
            yunPrice: yunPrice
        });
    },
    wuliuDetailsTap: function(e) {
        var orderId = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: "/pages/wuliu/index?id=" + orderId
        })
    },
    confirmBtnTap: function(e) {
        var that = this;
        var orderId = e.currentTarget.dataset.id;
        wx.showModal({
            title: '确认您已收到商品？',
            content: '',
            success: function(res) {
                if (res.confirm) {
                    wx.showLoading();
                    wx.request({
                        url: 'https://small.kuaiduodian.com/' + '/order/delivery' + '?mallname=' + app.globalData.subDomain,
                        data: {
                            token: app.globalData.token,
                            orderId: orderId
                        },
                        success: (res) => {
                            wx.request({
                                url: 'https://small.kuaiduodian.com/' + '/order/reputation' + '?mallname=' + app.globalData.subDomain,
                                data: {
                                    token: app.globalData.token,
                                    orderId: orderId,
                                    reputation: 2
                                },
                                success: (res) => {
                                    wx.hideLoading();
                                    if (res.data.code == 0) {
                                        that.onShow();
                                    }
                                }
                            })
                        }
                    })
                }
            }
        })
    }
})