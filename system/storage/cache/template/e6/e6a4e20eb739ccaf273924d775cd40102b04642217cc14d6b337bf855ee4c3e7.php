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

/* extension/payment/razorpay.twig */
class __TwigTemplate_904a6fe35a50b0d43c24b7204365d5edf76ba7f18a52810f571f2c6187508bbf extends \Twig\Template
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
        echo ($context["header"] ?? null);
        echo ($context["column_left"] ?? null);
        echo "
<div id=\"content\">
  <div class=\"page-header\">
    <div class=\"container-fluid\">
      <div class=\"pull-right\">
        <button type=\"submit\" form=\"form-razorpay\" data-toggle=\"tooltip\" title=\"";
        // line 6
        echo ($context["button_save"] ?? null);
        echo "\" class=\"btn btn-primary\"><i class=\"fa fa-save\"></i></button>
        <a href=\"";
        // line 7
        echo ($context["cancel"] ?? null);
        echo "\" data-toggle=\"tooltip\" title=\"";
        echo ($context["button_cancel"] ?? null);
        echo "\" class=\"btn btn-default\"><i class=\"fa fa-reply\"></i></a></div>
      <h1>";
        // line 8
        echo ($context["heading_title"] ?? null);
        echo "</h1>
      <ul class=\"breadcrumb\">
        ";
        // line 10
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["breadcrumbs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["breadcrumb"]) {
            // line 11
            echo "        <li><a href=\"";
            echo twig_get_attribute($this->env, $this->source, $context["breadcrumb"], "href", [], "any", false, false, false, 11);
            echo "\">";
            echo twig_get_attribute($this->env, $this->source, $context["breadcrumb"], "text", [], "any", false, false, false, 11);
            echo "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['breadcrumb'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "      </ul>
    </div>
  </div>
  <div class=\"container-fluid\">
    ";
        // line 17
        if (($context["error_warning"] ?? null)) {
            // line 18
            echo "    <div class=\"alert alert-danger\"><i class=\"fa fa-exclamation-circle\"></i> ";
            echo ($context["error_warning"] ?? null);
            echo "
      <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
    </div>
    ";
        }
        // line 22
        echo "    <div class=\"panel panel-default\">
      <div class=\"panel-heading\">
        <h3 class=\"panel-title\"><i class=\"fa fa-pencil\"></i> ";
        // line 24
        echo ($context["text_edit"] ?? null);
        echo "</h3>
      </div>
      <div class=\"panel-body\">
        <form action=\"";
        // line 27
        echo ($context["action"] ?? null);
        echo "\" method=\"post\" enctype=\"multipart/form-data\" id=\"form-razorpay\" class=\"form-horizontal\">
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-key-id\"><span data-toggle=\"tooltip\" title=\"";
        // line 29
        echo ($context["help_key_id"] ?? null);
        echo "\">";
        echo ($context["entry_key_id"] ?? null);
        echo "</span></label>
            <div class=\"col-sm-10\">
              <input type=\"text\" name=\"payment_razorpay_key_id\" value=\"";
        // line 31
        echo ($context["razorpay_key_id"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_key_id"] ?? null);
        echo "\" id=\"input-key-id\" class=\"form-control\" />
              ";
        // line 32
        if (($context["error_key_id"] ?? null)) {
            // line 33
            echo "              <div class=\"text-danger\">";
            echo ($context["error_key_id"] ?? null);
            echo "</div>
              ";
        }
        // line 35
        echo "            </div>
          </div>
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-key-secret\">";
        // line 38
        echo ($context["entry_key_secret"] ?? null);
        echo "</label>
            <div class=\"col-sm-10\">
              <input type=\"text\" name=\"payment_razorpay_key_secret\" value=\"";
        // line 40
        echo ($context["razorpay_key_secret"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_key_secret"] ?? null);
        echo "\" id=\"input-key-secret\" class=\"form-control\" />
              ";
        // line 41
        if (($context["error_key_secret"] ?? null)) {
            // line 42
            echo "              <div class=\"text-danger\">";
            echo ($context["error_key_secret"] ?? null);
            echo "</div>
              ";
        }
        // line 44
        echo "            </div>
          </div>
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-order-status\"><span data-toggle=\"tooltip\" title=\"";
        // line 47
        echo ($context["help_order_status"] ?? null);
        echo "\">";
        echo ($context["entry_order_status"] ?? null);
        echo "</span></label>
            <div class=\"col-sm-10\">
              <select name=\"payment_razorpay_order_status_id\" id=\"input-order-status\" class=\"form-control\">
                ";
        // line 50
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["order_statuses"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["order_status"]) {
            // line 51
            echo "                  ";
            if (((($context["razorpay_order_status_id"] ?? null) && (twig_get_attribute($this->env, $this->source, $context["order_status"], "order_status_id", [], "any", false, false, false, 51) == ($context["razorpay_order_status_id"] ?? null))) || (twig_get_attribute($this->env, $this->source, $context["order_status"], "order_status_id", [], "any", false, false, false, 51) == 2))) {
                // line 52
                echo "                    <option value=\"";
                echo twig_get_attribute($this->env, $this->source, $context["order_status"], "order_status_id", [], "any", false, false, false, 52);
                echo "\" selected=\"selected\">";
                echo twig_get_attribute($this->env, $this->source, $context["order_status"], "name", [], "any", false, false, false, 52);
                echo "</option>
                  ";
            } else {
                // line 54
                echo "                    <option value=\"";
                echo twig_get_attribute($this->env, $this->source, $context["order_status"], "order_status_id", [], "any", false, false, false, 54);
                echo "\">";
                echo twig_get_attribute($this->env, $this->source, $context["order_status"], "name", [], "any", false, false, false, 54);
                echo "</option>
                  ";
            }
            // line 56
            echo "                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['order_status'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 57
        echo "              </select>
            </div>
          </div>
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-status\">";
        // line 61
        echo ($context["entry_status"] ?? null);
        echo "</label>
            <div class=\"col-sm-10\">
              <select name=\"payment_razorpay_status\" id=\"input-status\" class=\"form-control\">
                ";
        // line 64
        if (($context["razorpay_status"] ?? null)) {
            // line 65
            echo "                <option value=\"1\" selected=\"selected\">";
            echo ($context["text_enabled"] ?? null);
            echo "</option>
                <option value=\"0\">";
            // line 66
            echo ($context["text_disabled"] ?? null);
            echo "</option>
                ";
        } else {
            // line 68
            echo "                <option value=\"1\">";
            echo ($context["text_enabled"] ?? null);
            echo "</option>
                <option value=\"0\" selected=\"selected\">";
            // line 69
            echo ($context["text_disabled"] ?? null);
            echo "</option>
                ";
        }
        // line 71
        echo "              </select>
            </div>
          </div>
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-sort-order\">";
        // line 75
        echo ($context["entry_sort_order"] ?? null);
        echo "</label>
            <div class=\"col-sm-10\">
              <input type=\"text\" name=\"payment_razorpay_sort_order\" value=\"";
        // line 77
        echo ($context["razorpay_sort_order"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_sort_order"] ?? null);
        echo "\" id=\"input-sort-order\" class=\"form-control\" />
            </div>
          </div>

          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-pay-action\">";
        // line 82
        echo ($context["entry_payment_action"] ?? null);
        echo "</label>
            <div class=\"col-sm-10\">
              <select name=\"payment_razorpay_payment_action\" id=\"input-pay-action\" class=\"form-control\">
                ";
        // line 85
        if (( !($context["razorpay_payment_action"] ?? null) || (($context["razorpay_payment_action"] ?? null) == "capture"))) {
            echo " 
                <option value=\"capture\" selected=\"selected\">";
            // line 86
            echo ($context["text_capture"] ?? null);
            echo "</option>
                <option value=\"authorize\">";
            // line 87
            echo ($context["text_authorize"] ?? null);
            echo "</option>
                ";
        } else {
            // line 89
            echo "                <option value=\"capture\">";
            echo ($context["text_capture"] ?? null);
            echo "</option>
                <option value=\"authorize\" selected=\"selected\">";
            // line 90
            echo ($context["text_authorize"] ?? null);
            echo "</option>
                ";
        }
        // line 92
        echo "              </select>
            </div>
          </div>

          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-max-capture-delay\"><span data-toggle=\"tooltip\" title=\"";
        // line 97
        echo ($context["help_max_delay"] ?? null);
        echo "\">";
        echo ($context["entry_max_capture_delay"] ?? null);
        echo "</span></label>
            <div class=\"col-sm-10\">
              <input type=\"text\" name=\"payment_razorpay_max_capture_delay\" value=\"";
        // line 99
        echo ($context["razorpay_max_capture_delay"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_max_capture_delay1"] ?? null);
        echo "\" id=\"input-max-capture-delay\" class=\"form-control\" />
            </div>
          </div>

          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-webhook-status\">";
        // line 104
        echo ($context["entry_webhook_status"] ?? null);
        echo "</label>
            <div class=\"col-sm-10\">
              <select name=\"payment_razorpay_webhook_status\" id=\"input-webhook-status\" class=\"form-control\">
                ";
        // line 107
        if (($context["razorpay_webhook_status"] ?? null)) {
            // line 108
            echo "                <option value=\"1\" selected=\"selected\">";
            echo ($context["text_enabled"] ?? null);
            echo "</option>
                <option value=\"0\">";
            // line 109
            echo ($context["text_disabled"] ?? null);
            echo "</option>
                ";
        } else {
            // line 111
            echo "                <option value=\"1\">";
            echo ($context["text_enabled"] ?? null);
            echo "</option>
                <option value=\"0\" selected=\"selected\">";
            // line 112
            echo ($context["text_disabled"] ?? null);
            echo "</option>
                ";
        }
        // line 114
        echo "              </select>
            </div>
          </div>
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-webhook-url\"><span data-toggle=\"tooltip\" title=\"";
        // line 118
        echo ($context["help_webhook_url"] ?? null);
        echo "\">";
        echo ($context["entry_webhook_url"] ?? null);
        echo "</span></label>
            <div class=\"col-sm-10\">
              <div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-link\"></i></span>
                <input type=\"text\" readonly value=\"";
        // line 121
        echo ($context["razorpay_webhook_url"] ?? null);
        echo "\" id=\"input-webhook-url\" class=\"form-control\" />
              </div>
            </div>
          </div>
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-webhook-secret\">";
        // line 126
        echo ($context["entry_webhook_secret"] ?? null);
        echo "</label>
            <div class=\"col-sm-10\">
              <input type=\"text\" name=\"payment_razorpay_webhook_secret\" value=\"";
        // line 128
        echo ($context["razorpay_webhook_secret"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_webhook_secret"] ?? null);
        echo "\" id=\"input-webhook-secret\" class=\"form-control\" />
              ";
        // line 129
        if (($context["error_webhook_secret"] ?? null)) {
            // line 130
            echo "              <div class=\"text-danger\">";
            echo ($context["error_webhook_secret"] ?? null);
            echo "</div>
              ";
        }
        // line 132
        echo "            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
";
        // line 139
        echo ($context["footer"] ?? null);
    }

    public function getTemplateName()
    {
        return "extension/payment/razorpay.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  380 => 139,  371 => 132,  365 => 130,  363 => 129,  357 => 128,  352 => 126,  344 => 121,  336 => 118,  330 => 114,  325 => 112,  320 => 111,  315 => 109,  310 => 108,  308 => 107,  302 => 104,  292 => 99,  285 => 97,  278 => 92,  273 => 90,  268 => 89,  263 => 87,  259 => 86,  255 => 85,  249 => 82,  239 => 77,  234 => 75,  228 => 71,  223 => 69,  218 => 68,  213 => 66,  208 => 65,  206 => 64,  200 => 61,  194 => 57,  188 => 56,  180 => 54,  172 => 52,  169 => 51,  165 => 50,  157 => 47,  152 => 44,  146 => 42,  144 => 41,  138 => 40,  133 => 38,  128 => 35,  122 => 33,  120 => 32,  114 => 31,  107 => 29,  102 => 27,  96 => 24,  92 => 22,  84 => 18,  82 => 17,  76 => 13,  65 => 11,  61 => 10,  56 => 8,  50 => 7,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "extension/payment/razorpay.twig", "");
    }
}
