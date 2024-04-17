<?php

namespace Orchestra\templates;

/**
 * --------------------
 * Template Class
 * --------------------
 * 
 * This class holds the basic functionality
 * and logic to render Web Pages directly from the server
 * side based on the url provided by the middleware and the
 * endpoint. 
 * 
 * NB - Template names do not need to be the same as the endpoint in order
 * to work as you specify the template name in the Router method you're using.
 * 
 * For e.g. /auth/login -> renders the login.html page (name is not case sensitive)
 * For e.g. /auth/register -> renders the register.html page (name is not case sensitive)
 */
class Template { 

   protected $basePath;

   public function __construct($basePath = ""){
      $this->basePath = $basePath;
   }

   public function render($template, $data = []) {
      $templateFilePath = $this->basePath . '/' . $template;
      
      if (!file_exists($templateFilePath)) {
          throw new \Exception("Template file not found: $template");
      }

      
      // Read template file contents
      $templateContent = file_get_contents($templateFilePath);
      
      
      // Replace placeholders with data
      $templateContent = $this->parseTemplate($templateContent, $data);
      
      // Output rendered template
      return $templateContent;
  }

  public function view($template, $data = []){
      $templatePath = dirname(__DIR__) . "/../core/templates/$template";
      if (!file_exists($templatePath)) {
         throw new \Exception("Template file not found: $template");
     }

     
     $templateContent = file_get_contents($templatePath);
      
      
      // Replace placeholders with data
      $templateContent = $this->parseTemplate($templateContent, $data);

      echo $templateContent;
  }

  protected function parseTemplate($template, $context) {
         // Handle variables and loops in a single pass
         $template = preg_replace_callback('/\{\{ (\w+) \}\}|\{% for (\w+) in (\w+) %\}(.*?)\{% endfor %\}/s', function ($matches) use ($context) {
         // Check if it's a variable replacement
         if (!empty($matches[1])) {
            $variableName = $matches[1];
            $replacement = $context[$variableName] ?? '';
            return $replacement;
         } else {
            // It's a loop replacement
            $loopVariable = $matches[2];
            $loopArray = $context[$matches[3]] ?? [];
            $loopContent = $matches[4];

            $output = '';
            // Replace loop content with items
            foreach ($loopArray as $item) {
                  // Replace loop variable in loop content
                  $replacedContent = str_replace("{{ $loopVariable }}", $item, $loopContent);                  
                  $output .= $replacedContent;
            }
            return $output;
         }
      }, $template);
      return $template;
   }


}
