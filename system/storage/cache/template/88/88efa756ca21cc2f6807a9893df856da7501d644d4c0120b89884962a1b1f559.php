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

/* common/login.twig */
class __TwigTemplate_ef76b62198525418e0456f345e3e67872a3c26ba04ddddb17ac819f09d619e65 extends \Twig\Template
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
        echo "
<div id=\"content\">
  <div class=\"container-fluid\"><br />
    <br />
    <div class=\"row\">
      <div class=\"col-sm-offset-4 col-sm-4\">
        <div class=\"panel panel-default\">
          <div class=\"panel-heading\">
            <h1 class=\"panel-title\"><i class=\"fa fa-lock\"></i> ";
        // line 9
        echo ($context["text_login"] ?? null);
        echo "</h1>
          </div>
          <div class=\"panel-body\">
            ";
        // line 12
        if (($context["success"] ?? null)) {
            // line 13
            echo "            <div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> ";
            echo ($context["success"] ?? null);
            echo "
              <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
            </div>
            ";
        }
        // line 17
        echo "            ";
        if (($context["error_warning"] ?? null)) {
            // line 18
            echo "            <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
            echo ($context["error_warning"] ?? null);
            echo "
              <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
            </div>
            ";
        }
        // line 22
        echo "            <form action=\"";
        echo ($context["action"] ?? null);
        echo "\" method=\"post\" enctype=\"multipart/form-data\">
              <div class=\"form-group\">
                <label for=\"input-username\">";
        // line 24
        echo ($context["entry_username"] ?? null);
        echo "</label>
                <div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-user\"></i></span>
                  <input type=\"text\" name=\"username\" value=\"";
        // line 26
        echo ($context["username"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_username"] ?? null);
        echo "\" id=\"input-username\" class=\"form-control\" />
                </div>
              </div>
              <div class=\"form-group\">
                <label for=\"input-password\">";
        // line 30
        echo ($context["entry_password"] ?? null);
        echo "</label>
                <div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-lock\"></i></span>
                  <input type=\"password\" name=\"password\" value=\"";
        // line 32
        echo ($context["password"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_password"] ?? null);
        echo "\" id=\"input-password\" class=\"form-control\" />
                </div>
                ";
        // line 34
        if (($context["forgotten"] ?? null)) {
            // line 35
            echo "                <span class=\"help-block\"><a href=\"";
            echo ($context["forgotten"] ?? null);
            echo "\">";
            echo ($context["text_forgotten"] ?? null);
            echo "</a></span>
                ";
        }
        // line 37
        echo "              </div>
              
              <div class=\"text-right\">
                <button type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-key\"></i> ";
        // line 40
        echo ($context["button_login"] ?? null);
        echo "</button>
              </div>
              ";
        // line 42
        if (($context["redirect"] ?? null)) {
            // line 43
            echo "              <input type=\"hidden\" name=\"redirect\" value=\"";
            echo ($context["redirect"] ?? null);
            echo "\" />
              ";
        }
        // line 45
        echo "            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
        // line 52
        echo ($context["footer"] ?? null);
    }

    public function getTemplateName()
    {
        return "common/login.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  144 => 52,  135 => 45,  129 => 43,  127 => 42,  122 => 40,  117 => 37,  109 => 35,  107 => 34,  100 => 32,  95 => 30,  86 => 26,  81 => 24,  75 => 22,  67 => 18,  64 => 17,  56 => 13,  54 => 12,  48 => 9,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/login.twig", "");
    }
}
