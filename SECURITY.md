# Segurança

## Reporte

Não publique chaves, senhas, dumps de banco ou descrições reais de chamados em issues públicas.

## Controles

- prepared statements para consultas;
- escape de saída nas views;
- CSRF em operações de escrita;
- autorização por perfil e propriedade;
- sessão com `HttpOnly`, `SameSite=Lax` e `Secure` em HTTPS;
- minimização de dados antes da integração externa;
- respostas brutas da API não são registradas;
- endpoint público de diagnóstico removido.

## Risco residual

O limite de login usa a sessão e serve como proteção básica. Uma implantação pública deve adicionar rate limiting por IP, HTTPS, monitoramento, backups protegidos e rotação periódica de credenciais.
