<template>
    <div>
        <div :id="'paypal-button' + payment.id"></div>
    </div>
</template>

<style>
.paypal-button.paypal-button-context-iframe {
  text-align: center;
}
</style>
<script>
export default {
  props: ['payment', 'order'],
  data: function () {
    return {};
  },
  mounted() {
    let app = this;
    app.$loadScript("https://www.paypalobjects.com/api/checkout.js").then(() => {
      paypal.Button.render({
        env: app.payment.settings.mode == 0 ? 'sandbox' : 'production',
        client: {
          sandbox: app.payment.settings.client_id,
          production: app.payment.settings.client_id
        },
        locale: 'en_US',
        style: {
          size: 'medium',
          color: 'gold',
          shape: 'pill',
        },
        commit: true,
        payment: function (resolve, reject) {
          //Here call your own API server
          return new Promise((resolve, reject) => {
            axios.post('/pay-pal/create-payment/' + app.payment.id, {
              amount: app.order.subtotal,
              orderId: app.order.id
            })
                .then(res => {
                  resolve(res.data.id)
                })
                .catch(error => {
                  reject(error)
                })
          })
        },
        onAuthorize: function (data) {
          // Execute the payment here, when the buyer approves the transaction
          return new Promise((resolve, reject) => {
            axios.post('/pay-pal/execute-paypal/' + app.payment.id, {
              payerId: data.payerID,
              paymentId: data.paymentID,
              orderId: app.order.id,
            })
                .then(function (resp) {
                  resolve(resp)
                  app.$emit('order-completed');
                })
                .catch(error => {
                  reject(error)
                })
                    })
                }
            }, '#paypal-button' + app.payment.id);
        })
    }
}
</script>
