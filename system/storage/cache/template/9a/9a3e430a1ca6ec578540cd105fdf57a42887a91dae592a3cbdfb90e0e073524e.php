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

/* default/template/common/stores.twig */
class __TwigTemplate_71de27aae8c03a8ebd3b4195e66f1902e7fa303d236168efc568d59a3f4fcf0c extends \Twig\Template
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
<div id=\"search\" class=\"input-group\">
  
 ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["stores"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["store"]) {
            // line 5
            echo "
    <li><a href=\"";
            // line 6
            echo twig_get_attribute($this->env, $this->source, $context["store"], "href", [], "any", false, false, false, 6);
            echo "\" target=\"_blank\">";
            echo twig_get_attribute($this->env, $this->source, $context["store"], "name", [], "any", false, false, false, 6);
            echo " </a>
             
<img src=\"";
            // line 8
            echo twig_get_attribute($this->env, $this->source, $context["store"], "url", [], "any", false, false, false, 8);
            echo "\" alt=\" ";
            echo ($context["storeowner"] ?? null);
            echo "\" title= \"";
            echo ($context["storeowner"] ?? null);
            echo "\"/>

      </li>

 
<br>

";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['store'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 16
        echo "
</div>";
    }

    public function getTemplateName()
    {
        return "default/template/common/stores.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 16,  57 => 8,  50 => 6,  47 => 5,  43 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "default/template/common/stores.twig", "");
    }
}
