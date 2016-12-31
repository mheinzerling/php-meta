<?php
declare(strict_types = 1);
namespace mheinzerling\meta\writer;


use mheinzerling\meta\language\AClass;
use mheinzerling\meta\language\ANamespace;

class ClassWriter
{
    const USEBLOCK = "__USE__";
    /**
     * @var string
     */
    private $name;
    /**
     * @var AClass
     */
    private $extends;
    /**
     * @var ANamespace
     */
    private $namespace;
    /**
     * @var FieldWriter[]
     */
    private $fields = [];
    /**
     * @var MethodWriter[]
     */
    private $methods = [];
    /**
     * @var string
     */
    private $doc;
    /**
     * @var mixed[]
     */
    private $constants = [];
    /**
     * @var AClass[]
     */
    private $use = [];
    /**
     * @var bool
     */
    private $final = false;
    /**
     * @var bool
     */
    private $abstract = false;


    public function __construct(string $simpleName)
    {
        $this->name = $simpleName;
        $this->namespace = new ANamespace("\\");
    }

    public function extends (AClass $class): ClassWriter
    {
        $this->extends = $class;
        return $this;
    }

    public function namespace(ANamespace $namespace): ClassWriter
    {
        $this->namespace = $namespace;
        return $this;
    }

    private function line(string $line): string
    {
        return $line . "\n";
    }

    public function write(): string
    {
        $result = $this->line("<?php");
        $result .= $this->line("declare(strict_types = 1);");
        $result .= $this->line("");
        if (!$this->namespace->isRoot()) {
            $result .= $this->line("namespace " . $this->namespace->qualified() . ";");
            $result .= $this->line("");
        }
        $result .= self::USEBLOCK;
        if (!empty($this->doc)) {
            $result .= $this->line("/**");
            $result .= $this->doc;
            $result .= $this->line(" */");
        }
        $definition = "";
        if ($this->abstract) $definition .= "abstract ";
        if ($this->final) $definition .= "final ";
        $definition .= "class " . $this->name;
        if (!empty($this->extends))
            $definition .= " extends " . $this->print($this->extends);
        $result .= $this->line($definition);
        $result .= $this->line("{");
        foreach ($this->constants as $name => $value) {
            if (is_numeric($value)) $result .= $this->line("    const $name = $value;");
            else if (is_bool($value)) $result .= $this->line("    const $name = " . ($value ? "true" : "false") . ";");
            //TODO array
            else $result .= $this->line("    const $name = '$value';");
        }

        foreach ($this->fields as $fieldWriter) {
            $result .= $fieldWriter->create();
            $result .= $this->line("");
        }
        foreach ($this->methods as $methodWriter) {
            $result .= $methodWriter->create();
            $result .= $this->line("");
        }
        $result = trim($result);
        $result .= $this->line("");
        $result .= $this->line("}");

        foreach ($this->use as $u) {
            $result = str_replace(self::USEBLOCK, $this->line("use " . $u->import() . ";") . self::USEBLOCK, $result);
        }
        if (count($this->use) > 0) $result = str_replace(self::USEBLOCK, $this->line("") . self::USEBLOCK, $result);
        $result = str_replace(self::USEBLOCK, "", $result);

        return trim($result);

    }

    public function method(string $name): MethodWriter
    {
        $methodWriter = new MethodWriter($this, $name);
        $this->methods[$name] = $methodWriter;
        return $methodWriter;
    }

    public function field(string $name): FieldWriter
    {
        $fieldWriter = new FieldWriter($this, $name);
        $this->fields[$name] = $fieldWriter;
        return $fieldWriter;
    }

    public function doc(string $line): ClassWriter
    {
        $this->doc .= $this->line(rtrim(" * " . $line));
        return $this;
    }

    public function const(string $name, $value): ClassWriter
    {
        $this->constants[$name] = $value;
        return $this;
    }

    public function final(bool $final = true): ClassWriter
    {
        $this->final = $final;
        return $this;
    }

    public function use (AClass $class): ClassWriter
    {
        if ($class->getNamespace() == $this->namespace) return $this;

        $this->use[$class->import()] = $class;

        uksort($this->use, function ($a, $b) {
            $partsA = explode(ANamespace::DELIMITER, $a);
            $partsB = explode(ANamespace::DELIMITER, $b);
            for ($c = 0, $s = max(count($partsA), count($partsB)); $c < $s; $c++) {
                if (!isset($partsA[$c])) return -1;
                if (!isset($partsB[$c])) return 1;
                if ($partsA[$c] != $partsB[$c]) return $partsA[$c]<=>$partsB[$c];
            }
            return 0;
        });
        return $this;
    }

    public function abstract ($abstract = true): ClassWriter
    {
        $this->abstract = $abstract;
        return $this;
    }

    public function getNamespace(): ANamespace
    {
        return $this->namespace;
    }

    public function print(AClass $class): string //TODO relative namespaces
    {
        if ($class->getNamespace()->isRoot()) {
            if ($this->getNamespace()->isRoot()) return $class->simple();
            return $class->fullyQualified();
        }
        $this->use($class);
        return $class->simple();
    }
}