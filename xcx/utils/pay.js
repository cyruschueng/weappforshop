function wxpay(app, money, orderId, redirectUrl) {
    wx.request({
        url: 'https://small.kuaiduodian.com/' + '/pay/wxapp_get_pay_data' + '?mallname=' + app.globalData.subDomain,
        data: {
            token: app.globalData.token,
            money: money,
            order_id: orderId,
            remark: "支付订单 ：" + orderId,
            payName: "在线支付",
            nextAction: { type: 0, id: orderId }
        },
        //method:'POST',
        success: function(res) {
            console.log('api result:');
            console.log(res.data);
            if (res.data.code == 0) {
                // 发起支付
                wx.requestPayment({
                    timeStamp: res.data.data.timeStamp,
                    nonceStr: res.data.data.nonceStr,
                    package: res.data.data.package,
                    signType: res.data.data.signType,
                    paySign: res.data.data.paySign,
                    fail: function(aaa) {
                                         wx.showToast({ title: '支付失败' })
                        // wx.showModal({ title: '支付失败:'  ,      content: aaa.errMsg  , showCancel:false})
                    },
                    success: function() {
                        wx.showToast({ title: '支付成功' })
                        wx.reLaunch({
                            url: redirectUrl
                        });
                    }
                })
            } else {
              wx.showToast({ title: '' + res.data.data })
            }
        }
    })
}

module.exports = {
    wxpay: wxpay
}