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
class __TwigTemplate_87518903fdb07ab8fcfc78cc561d324470fdf0fb9a112a8b5da53b14582b4b35 extends \Twig\Template
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
        echo ($context["hello"] ?? null);
        echo "</h1>
          </div>
          <div class=\"panel-body\">

            ";
        // line 13
        if (($context["success"] ?? null)) {
            // line 14
            echo "            <div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> ";
            echo ($context["success"] ?? null);
            echo "
              <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
            </div>
            ";
        }
        // line 18
        echo "
            ";
        // line 19
        if (($context["error_warning"] ?? null)) {
            // line 20
            echo "            <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
            echo ($context["error_warning"] ?? null);
            echo "
              <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
            </div>
            ";
        }
        // line 24
        echo "
            <form action=\"";
        // line 25
        echo ($context["action"] ?? null);
        echo "\" method=\"post\" enctype=\"multipart/form-data\">
              <div class=\"form-group\">
                <label for=\"input-username\">";
        // line 27
        echo ($context["entry_username"] ?? null);
        echo "</label>
                <div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-user\"></i></span>
                  <input type=\"text\" name=\"username\" value=\"";
        // line 29
        echo ($context["username"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_username"] ?? null);
        echo "\" id=\"input-username\" class=\"form-control\" />
                </div>
              </div>

              <div class=\"form-group\">
                <label for=\"input-password\">";
        // line 34
        echo ($context["entry_password"] ?? null);
        echo "</label>
                <div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-lock\"></i></span>
                  <input type=\"password\" name=\"password\" value=\"";
        // line 36
        echo ($context["password"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_password"] ?? null);
        echo "\" id=\"input-password\" class=\"form-control\" />
                </div>

                ";
        // line 39
        if (($context["forgotten"] ?? null)) {
            // line 40
            echo "                <span class=\"help-block\"><a href=\"";
            echo ($context["forgotten"] ?? null);
            echo "\">";
            echo ($context["text_forgotten"] ?? null);
            echo "</a></span>
                ";
        }
        // line 42
        echo "                
              </div>
              
              <div class=\"text-right\">
                <button type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-key\"></i> ";
        // line 46
        echo ($context["button_login"] ?? null);
        echo "</button>
              </div>
              ";
        // line 48
        if (($context["redirect"] ?? null)) {
            // line 49
            echo "              <input type=\"hidden\" name=\"redirect\" value=\"";
            echo ($context["redirect"] ?? null);
            echo "\" />
              ";
        }
        // line 51
        echo "            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
        // line 58
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
        return array (  152 => 58,  143 => 51,  137 => 49,  135 => 48,  130 => 46,  124 => 42,  116 => 40,  114 => 39,  106 => 36,  101 => 34,  91 => 29,  86 => 27,  81 => 25,  78 => 24,  70 => 20,  68 => 19,  65 => 18,  57 => 14,  55 => 13,  48 => 9,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/login.twig", "");
    }
}
