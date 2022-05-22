<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* default/template/extension/payment/razorpay.twig */
class __TwigTemplate_eff85431449e4dcf70b18f7f8cbb434fa624cc4762757671dd60ef5c92e88882 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        if (($context["is_hosted"] ?? null)) {
            // line 2
            echo "
<form action=\"";
            // line 3
            echo ($context["api_url"] ?? null);
            echo "checkout/embedded\" method=\"post\" name=\"embedded_checkout_form\">
    <input type=\"hidden\" name=\"key_id\" value=\"";
            // line 4
            echo ($context["key_id"] ?? null);
            echo "\">
    <input type=\"hidden\" name=\"order_id\" value=\"";
            // line 5
            echo ($context["razorpay_order_id"] ?? null);
            echo "\">
    <input type=\"hidden\" name=\"image\" value=\"";
            // line 6
            echo ($context["image"] ?? null);
            echo "\">
    <input type=\"hidden\" name=\"name\" value=\"";
            // line 7
            echo ($context["name"] ?? null);
            echo "\">
    <input type=\"hidden\" name=\"description\" value=\"Order # ";
            // line 8
            echo ($context["merchant_order_id"] ?? null);
            echo "\">
    
    <input type=\"hidden\" name=\"prefill[name]\" value=\"";
            // line 10
            echo ($context["card_holder_name"] ?? null);
            echo "\"> 
    <input type=\"hidden\" name=\"prefill[contact]\" value=\"";
            // line 11
            echo ($context["phone"] ?? null);
            echo "\">  
    <input type=\"hidden\" name=\"prefill[email]\" value=\"";
            // line 12
            echo ($context["email"] ?? null);
            echo "\">
    
    <input type=\"hidden\" name=\"notes[opencart_order_id]\" value=\"";
            // line 14
            echo ($context["merchant_order_id"] ?? null);
            echo "\">

    <input type=\"hidden\" name=\"_[integration]\" value=\"opencart\"> 
    <input type=\"hidden\" name=\"_[integration_version]\" value=\"";
            // line 17
            echo ($context["version"] ?? null);
            echo "\">  
    <input type=\"hidden\" name=\"_[integration_parent_version]\" value=\"";
            // line 18
            echo ($context["oc_version"] ?? null);
            echo "\">

    <input type=\"hidden\" name=\"callback_url\" value=\"";
            // line 20
            echo ($context["return_url"] ?? null);
            echo "\">
    <input type=\"hidden\" name=\"cancel_url\" value=\"";
            // line 21
            echo ($context["cancel_url"] ?? null);
            echo "\">
    <div class=\"buttons\">
      <div class=\"pull-right\">
        <input type=\"submit\" value=\"";
            // line 24
            echo ($context["button_confirm"] ?? null);
            echo "\" class=\"btn btn-primary\" />
      </div>
    </div>
  </form>

";
        } else {
            // line 30
            echo "
  <script data-cfasync='false' type='text/javascript' src=\"https://checkout.razorpay.com/v1/checkout.js\"></script>
  <script data-cfasync='false' type='text/javascript'>
    var razorpay_options = {
      key: \"";
            // line 34
            echo ($context["key_id"] ?? null);
            echo "\",
      amount: \"";
            // line 35
            echo ($context["total"] ?? null);
            echo "\",
      name: \"";
            // line 36
            echo ($context["name"] ?? null);
            echo "\",
      description: \"Order # ";
            // line 37
            echo ($context["merchant_order_id"] ?? null);
            echo "\",
      netbanking: true,
      currency: \"";
            // line 39
            echo ($context["currency_code"] ?? null);
            echo "\",
      prefill: {
        name:\"";
            // line 41
            echo ($context["card_holder_name"] ?? null);
            echo "\",
        email: \"";
            // line 42
            echo ($context["email"] ?? null);
            echo "\",
        contact: \"";
            // line 43
            echo ($context["phone"] ?? null);
            echo "\"
      },
      notes: {
        opencart_order_id: \"";
            // line 46
            echo ($context["merchant_order_id"] ?? null);
            echo "\"
      },
      _: {
          integration: 'opencart',
          integration_version: '";
            // line 50
            echo ($context["version"] ?? null);
            echo "',
          integration_parent_version: '";
            // line 51
            echo ($context["oc_version"] ?? null);
            echo "'
      },
      callback_url: \"";
            // line 53
            echo ($context["return_url"] ?? null);
            echo "\",
      order_id: \"";
            // line 54
            echo ($context["razorpay_order_id"] ?? null);
            echo "\",
      handler: function (transaction) {
          document.getElementById('razorpay_payment_id').value = transaction.razorpay_payment_id;
          document.getElementById('razorpay_signature').value = transaction.razorpay_signature;
          document.getElementById('razorpay-form').submit();
      }
    };
    var razorpay_submit_btn, razorpay_instance;

    function razorpaySubmit(el){
      if(typeof Razorpay == 'undefined'){
        setTimeout(razorpaySubmit, 200);
        if(!razorpay_submit_btn && el){
          razorpay_submit_btn = el;
          el.disabled = true;
          el.value = 'Please wait...';  
        }
      } else {
        if(!razorpay_instance){
          razorpay_instance = new Razorpay(razorpay_options);
          if(razorpay_submit_btn){
            razorpay_submit_btn.disabled = false;
            razorpay_submit_btn.value = \"";
            // line 76
            echo ($context["button_confirm"] ?? null);
            echo "\";
          }
        }
        razorpay_instance.open();
      }
    }

  </script>
  <form name=\"razorpay-form\" id=\"razorpay-form\" action=\"";
            // line 84
            echo ($context["return_url"] ?? null);
            echo "\" method=\"POST\">
    <input type=\"hidden\" name=\"razorpay_payment_id\" id=\"razorpay_payment_id\" />
    <input type=\"hidden\" name=\"razorpay_signature\" id=\"razorpay_signature\" />
  </form>
  <div class=\"buttons\">
    <div class=\"pull-right\">
      <input type=\"submit\" onclick=\"razorpaySubmit(this);\" value=\"";
            // line 90
            echo ($context["button_confirm"] ?? null);
            echo "\" class=\"btn btn-primary\" />
    </div>
  </div>

";
        }
    }

    public function getTemplateName()
    {
        return "default/template/extension/payment/razorpay.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  221 => 90,  212 => 84,  201 => 76,  176 => 54,  172 => 53,  167 => 51,  163 => 50,  156 => 46,  150 => 43,  146 => 42,  142 => 41,  137 => 39,  132 => 37,  128 => 36,  124 => 35,  120 => 34,  114 => 30,  105 => 24,  99 => 21,  95 => 20,  90 => 18,  86 => 17,  80 => 14,  75 => 12,  71 => 11,  67 => 10,  62 => 8,  58 => 7,  54 => 6,  50 => 5,  46 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "default/template/extension/payment/razorpay.twig", "");
    }
}
