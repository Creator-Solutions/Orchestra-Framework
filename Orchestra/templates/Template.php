<?php

namespace Orchestra\templates;

use Orchestra\env\EnvConfig;

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
class Template
{
   protected $templatePath;

   public function render($template, $data = [])
   {
      $templateFilePath = $this->templatePath . '/' . $template;

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

   public function view($template, $data = [])
   {
      $this->templatePath = dirname(__DIR__) . "/../app/resources/views/$template.pulse.php";
      if (!file_exists($this->templatePath)) {
         throw new \Exception("Template file not found: $template");
      }

      $templateContent = file_get_contents($this->templatePath);

      // Check for development mode
      if ($this->isDevelopment()) {
         // Insert React Refresh script
         $reactRefreshScript = '<script type="module">
                import RefreshRuntime from "http://localhost:5173/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.$RefreshReg$ = () => {}
                window.$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
            </script>';

         // Insert Vite client and dev entry point in the <body>
         $viteClientScript = '<script type="module" src="http://localhost:5173/@vite/client"></script>';
         $devEntryScript = '<script type="module" src="http://localhost:5173/index.tsx"></script>';
         $templateContent = str_replace('</body>', $reactRefreshScript . $viteClientScript . $devEntryScript . '</body>', $templateContent);
      } else {
         // Insert the production build script in the <body>
         $prodScript = '<script type="module" src="/public/assets/index-Bsv4HKnV.js"></script>';
         $templateContent = str_replace('</body>', $prodScript . '</body>', $templateContent);
      }

      // Parse template for any variables
      $templateContent = $this->parseTemplate($templateContent, $data);

      echo $templateContent;
   }

   protected function isDevelopment()
   {
      // Adjust this logic based on how you set the environment
      $env = new EnvConfig();

      return $env->getenv('APP_ENV') === 'development' || $_ENV['APP_ENV'] === 'development';
   }

   protected function parseTemplate($template, $context)
   {
      // Handle variables and loops in a single pass
      $template = preg_replace_callback('/\{\{ (\w+) \}\}|\{% for (\w+) in (\w+) %\}(.*?)\{% endfor %\}/s', function ($matches) use ($context) {
         if (!empty($matches[1])) {
            $variableName = $matches[1];
            return $context[$variableName] ?? '';
         } else {
            $loopVariable = $matches[2];
            $loopArray = $context[$matches[3]] ?? [];
            $loopContent = $matches[4];
            $output = '';
            foreach ($loopArray as $item) {
               $replacedContent = str_replace("{{ $loopVariable }}", $item, $loopContent);
               $output .= $replacedContent;
            }
            return $output;
         }
      }, $template);
      return $template;
   }
}
