<?php

namespace Orchestra\forte;


class ForteException extends ThrowableException
{

   public function constructor($code, $message = "Unkown error occurred"): self
   {
      switch ($code) {
         case 1:
            return new self("HEADERS_NOT_SET");
         case 2:
            return new self("USER_NOT_AUTHENTICATED");
         case 3:
            return new self("UNKNOWN_USER_AGENT");
      }

      return $this;
   }
}