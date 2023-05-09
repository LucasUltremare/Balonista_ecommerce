<?php
require_once 'mercadopago_config.php'; // Inclua o arquivo de configuração do Mercado Pago

$total = 0;
$qry = $conn->query("SELECT c.*,p.name,p.price,p.id as pid from `cart` c inner join `inventory` i on i.id=c.inventory_id inner join products p on p.id = i.product_id where c.client_id = ".$_settings->userdata('id'));
while($row = $qry->fetch_assoc()):
    $total += $row['price'] * $row['quantity'];
endwhile;

// Chave pública do Mercado Pago
$publicKey = 'SEU_PUBLIC_KEY'; // Substitua pela sua chave pública do Mercado Pago

// URLs de redirecionamento após o pagamento
$successUrl = 'https://yourdomain.com/success'; // URL de sucesso
$failureUrl = 'https://yourdomain.com/failure'; // URL de falha
$pendingUrl = 'https://yourdomain.com/pending'; // URL pendente

// Crie a preferência de pagamento no Mercado Pago
$preferenceData = array(
    'items' => array(
        array(
            'title' => 'Order',
            'quantity' => 1,
            'currency_id' => 'BRL',
            'unit_price' => $total
        )
    ),
    'back_urls' => array(
        'success' => $successUrl,
        'failure' => $failureUrl,
        'pending' => $pendingUrl
    )
);

$mp = new MP($publicKey); // Crie uma instância da classe MP com sua chave pública do Mercado Pago
$preference = $mp->create_preference($preferenceData);
$paymentUrl = $preference['response']['sandbox_init_point']; // Obtenha a URL de pagamento do Mercado Pago

?>

<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body">
                <h3 class="text-center"><b>Checkout</b></h3>
                <hr class="border-dark">
                <form action="" id="place_order">
                    <input type="hidden" name="amount" value="<?php echo $total ?>">
                    <input type="hidden" name="payment_method" value="cod">
                    <input type="hidden" name="paid" value="0">
                    <div class="row row-col-1 justify-content-center">
                        <div class="col-6">
                            <div class="form-group col mb-0">
                                <label for="" class="control-label">Order Type</label>
                            </div>
                            <div class="form-group d-flex pl-2">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input custom-control-input-primary" type="radio" id="customRadio4" name="order_type" value="1" checked="">
                                    <label for="customRadio4" class="custom-control-label">For Delivery</label>
                                </div>
                                <div class="custom-control custom-radio ml-3">
                                    <input class="custom-control-input custom-control-input-primary custom-control-input-outline" type="radio" id="customRadio5" name="order_type" value="2">
                                    <label for="customRadio5" class="custom-control-label">
                                    For Pick up</label>
                                </div>
                            </div>
                            <div class="form-group col address-holder">
                                <label for="" class="control-label">Delivery Address</label>
                                <textarea id="" cols="30" rows="3" name="delivery_address" class="form-control" style="resize:none"><?php echo $_settings->userdata('default_delivery_address') ?></textarea>
                            </div>
                            <div class="col">
                                <span><h4><b>Total:</b> <?php echo number_format($total) ?></h4></span>
                            </div>
                            <hr>
                            <div class="col my-3">
                                <h4 class="text-muted">Payment Method</h4>
                                <div class="d-flex w-100 justify-content-between">
                                    <a class="btn btn-flat btn-dark" href="javascript:void(0)" onclick="submitForm('cod')">Cash on Delivery</a>
                                    <a class="btn btn-flat btn-primary" href="<?php echo $paymentUrl; ?>">Mercado Pago</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    function submitForm(paymentMethod) {
        $('[name="payment_method"]').val(paymentMethod);
        $('[name="paid"]').val(1);
        $('#place_order').submit();
    }
</script>
