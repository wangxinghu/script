" Vim color file
"Version 1.1
" Last Change:	2006 August 1
"Modified to work with reloaded.vim AKA ps_color.vim
"as :color phpx
"   :color reloaded
"   :Reload 360  100  100  180    0    0    1    0    0    phpx.vim
"           +/-  +/-  +/-  +/-    ?  brite bg    ?   de    template
"           Hue  Sat  lt   Hu2   sat       1-lt  ?   bug   file
"                ura  dk         ura       0-dk  ?   0-n
"                tion            tion                1-y
"
"   Light experiments
"   :Reload 360 100 110 550 0 0 1 0 0 phpx.vim
"   :Reload 360 80 110 400 0 0 1 0 0 phpx.vim
"   :Reload 360 60 110 300 0 60 1 0 0 phpx.vim
"
"   Dark experiment
"   :Reload 360 100 100 200 0 200 0 0 0 phpx.vim
hi clear
if exists("syntax_on")
  syntax reset
endif

let g:colors_name = "phpx"
