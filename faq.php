<?php require_once 'config/config.php'; require_once 'includes/functions.php'; $pageTitle='FAQ - NS Beauty'; include 'includes/header.php'; ?>
<div class="page-banner"><div class="container"><h1>Frequently Asked Questions</h1></div></div>
<section class="section"><div class="container" style="max-width:800px">
<?php $faqs = [
['What is your return policy?','We offer hassle-free returns within 30 days of purchase. Products must be unused and in original packaging. Contact us to initiate a return.'],
['How long does delivery take?','Standard delivery takes 3-5 business days. Express delivery (1-2 days) is also available for select cities.'],
['Are your products cruelty-free?','Yes! All NS Beauty products are 100% cruelty-free. We never test on animals and use only ethically sourced ingredients.'],
['Can I use multiple coupon codes?','Only one coupon code can be applied per order. Choose the one that gives you the best discount!'],
['How do I track my order?','Once your order is shipped, you will receive an email with tracking information. You can also check your order status in your account dashboard.'],
['What payment methods do you accept?','We accept Cash on Delivery (COD), Credit/Debit Cards (Visa, Mastercard), JazzCash, EasyPaisa, and Bank Transfer.'],
['Are products authentic?','100% authentic! We source directly from manufacturers and our own production. Every product comes with our quality guarantee.'],
['How do I contact customer support?','You can reach us via our Contact Us page, WhatsApp at +92-300-1234567, or email at info@nsbeauty.com. We respond within 24 hours.'],
]; foreach ($faqs as $i => $faq): ?>
<div style="border:1px solid var(--gray-200);border-radius:12px;margin-bottom:12px;overflow:hidden">
  <div style="padding:18px 20px;font-weight:600;cursor:pointer;background:var(--off-white);display:flex;justify-content:space-between;align-items:center" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block';this.querySelector('i').className=this.nextElementSibling.style.display==='block'?'fas fa-minus':'fas fa-plus'">
    <?= $faq[0] ?><i class="fas fa-plus" style="color:var(--pink);font-size:0.85rem"></i>
  </div>
  <div style="display:none;padding:16px 20px;color:var(--gray-600);line-height:1.7;border-top:1px solid var(--gray-200)"><?= $faq[1] ?></div>
</div>
<?php endforeach; ?>
</div></section>
<?php include 'includes/footer.php'; ?>
