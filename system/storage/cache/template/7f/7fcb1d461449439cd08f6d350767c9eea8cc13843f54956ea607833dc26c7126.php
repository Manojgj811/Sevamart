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
class __TwigTemplate_ecca4a76e463654b88daebca4661cc410a0723c1e631de7b7d37d67045bd73a1 extends \Twig\Template
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
  <div class=\"container-fluid\"><br/>
    <br />
    <div class=\"row\">
      <div class=\"col-sm-offset-4 col-sm-4\">
        <div class=\"panel panel-default\">

          <div class=\"panel-heading\">
            <h6 class=\"panel-title\"><i class=\"fa fa-lock\"></i> ";
        // line 10
        echo ($context["name"] ?? null);
        echo "</h6>
          </div>

          <div class=\"panel-body\">
            ";
        // line 14
        if (($context["success"] ?? null)) {
            // line 15
            echo "            <div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> ";
            echo ($context["success"] ?? null);
            echo "
              <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
            </div>
            ";
        }
        // line 19
        echo "
            ";
        // line 20
        if (($context["error_warning"] ?? null)) {
            // line 21
            echo "            <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
            echo ($context["error_warning"] ?? null);
            echo "
              <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
            </div>
            ";
        }
        // line 25
        echo "
            <form action=\"";
        // line 26
        echo ($context["action"] ?? null);
        echo "\" method=\"post\" enctype=\"multipart/form-data\">
              <div class=\"form-group\">
                <label for=\"input-username\">";
        // line 28
        echo ($context["entry_username"] ?? null);
        echo "</label>
                <div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-user\"></i></span>
                  <input type=\"text\" name=\"username\" value=\"";
        // line 30
        echo ($context["username"] ?? null);
        echo "\" placeholder=\"";
        echo ($context["entry_username"] ?? null);
        echo "\" id=\"input-username\" class=\"form-control\" />
                </div>
              </div>

              <div class=\"form-group\">
                <label for=\"input-password\">";
        // line 35
        echo ($context["entry_password"] ?? null);
        echo "</label>
                <div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-\"></i></span>
                  <input type=\"password\" name=\"password\" value=\"";
        // line 37
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
        echo "              </div>
              
              <div class=\"text-right\">
                <button type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-key\"></i> ";
        // line 45
        echo ($context["button_login"] ?? null);
        echo "</button>
              </div>
              ";
        // line 47
        if (($context["redirect"] ?? null)) {
            // line 48
            echo "              <input type=\"hidden\" name=\"redirect\" value=\"";
            echo ($context["redirect"] ?? null);
            echo "\" />
              ";
        }
        // line 50
        echo "            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
        // line 57
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
        return array (  151 => 57,  142 => 50,  136 => 48,  134 => 47,  129 => 45,  124 => 42,  116 => 40,  114 => 39,  107 => 37,  102 => 35,  92 => 30,  87 => 28,  82 => 26,  79 => 25,  71 => 21,  69 => 20,  66 => 19,  58 => 15,  56 => 14,  49 => 10,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/login.twig", "");
    }
}
