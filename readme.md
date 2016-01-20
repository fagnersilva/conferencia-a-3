

O usuário A faz uma ligação normal para B, e após este atender utilizamos a transfência atendida (atxfer) e ligamos para C. Neste ponto estamos conversando com C, e B está em retenção ouvindo musica de espera, nada de novo até aqui. Agora a mágica: teclando *3 (ou qualquer coisa que se deseje) A, B e C são transferidos automáticamente para uma sala de conferência, sem anuncios nem bips!

features.conf, normalmente encontrado na pasta /etc/asterisk

conf3 => *3,caller,Macro,conf3-ini

Isto configura a combinação de teclas *3 (é aqui que você pode selecionar outra combinação de teclas) para acessar a macro conf3-ini. Certifique-se de que a combinação de teclas escolhida, no caso *3, não esteja em uso pelas outras funcionalidades já definidas acima, no mesmo arquivo. OK, terminamos com este arquivo.


Digite este comando na CLI do Asterisk para isso:

database put conf3 sala 100

Inclua o script em /usr/sbin/conf3Redirect.php

Lembre-se de editar os “define” no inicio do arquivo para uma credencial valida do seu manager.conf.

Certidique-se de ter o pacote php5-cli intalado para que o script possa rodar.

# Funcionamento

Ao acionar a tecla de conferência a três (*3 no caso) o dialplan verifica se o canal em questão tem algum canal em espera, através da variável de canal ${CHANNEL_ONHOLD} (que é setada pela nossa alteração no código fonte). Caso não tenha canal em espera, aborta.

Feito isso a macro transfere o canal bridge atual (seria o canal “C”, ligado por último) para a sala de conferência apontada pela entrada conf3/sala do astDB. Após o dialplan executa (em background, por causa do “&“) o script conf3Redirect.php.

Neste momento a trasnferência asistida é completada e os canais “A” e “B” voltam a se falar normalmente. Porem o script conf3Redirect.php que foi iniciado em background (após aguardar 1 segundo por segurança) envia um comando “Redirect” para o AMI, que irá redirecionar os canais “A” e “B” para a mesma sala em que “C” já está.

Feito tudo isso o script finaliza incrementando o numero da sala em conf3/sala do astDB para a próxima execução.
