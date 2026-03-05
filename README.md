
# Travel Requests Microservice ✈️

Microsserviço de gerenciamento de viagens corporativas desenvolvido com o ecossistema moderno do **Laravel 11** e **PHP 8.3**. A solução utiliza **MySQL** para persistência, **Redis** para processamento de filas e **Docker** para padronização de ambiente. A segurança é garantida via **JWT** com controle de acesso baseado em funções (**RBAC**). O projeto prioriza a escalabilidade através de **processamento assíncrono** e assegura alta confiabilidade com **90% de cobertura de testes** via **Pest**, integrados a um pipeline de CI com **GitHub Actions, Husky e Laravel Pint**. A API é totalmente documentada seguindo o padrão **OpenAPI 3.0** via **Swagger**, facilitando a integração e o consumo dos endpoints.


## 🧠 Decisões de Arquitetura e Design

O projeto foi construído seguindo padrões que visam a manutenibilidade, testabilidade granular e segurança auditável:

1. **Documentação OpenAPI 3.0 (Swagger)**: A API foi totalmente documentada utilizando o padrão **OpenAPI 3.0 via Swagger**. Isso garante uma interface interativa (DX - Developer Experience) onde é possível testar os endpoints, visualizar os contratos de dados (schemas), payloads de exemplo e os códigos de resposta HTTP semânticos diretamente pelo navegador.
2. **Action-Domain-Responder (ADR)**: A lógica de negócio foi extraída dos Controllers para **Actions** de responsabilidade única. Essa abordagem desacopla as regras de domínio da camada de transporte, facilitando a manutenção e o reaproveitamento de código.

3.  **Repository Pattern (Interface-Driven)**: Implementado para abstrair a camada de persistência através de contratos. Isso permite que a camada de domínio permaneça agnóstica ao ORM, facilitando mocks em testes unitários e a substituição da fonte de dados se necessário.

4.  **Service Layer (Interface-Driven)**: Camada de serviço dedicada para **JWT, Notificações e Logging**, sempre orientada a interfaces (DIP). Isso permite trocar implementações externas sem afetar o núcleo da aplicação.

5.  **Data Transfer Objects (DTOs)**: Utilizados para tipar e validar a entrada de dados entre a API e a camada de domínio, garantindo que as Actions operem apenas com dados estruturados e validados.

6.  **Form Requests & API Resources**: O **Form Requests** centraliza as regras de validação e autorização da requisição, enquanto o **API Resources** garante respostas consistentes e desacopladas da estrutura física do banco.

7.  **Migrations & Database Design (Dual-ID Strategy)**: Uso de chaves primárias e estrangeiras indexadas com **BigInt Autoincrement** para máxima performance interna no MySQL. Para o tráfego externo (API), utilizamos **UUIDs**, impedindo a exposição de IDs sequenciais e mitigando ataques de enumeração.

8.  **Segregação de Funções**: Aplicação rigorosa de segregação de permissões e funções, para integridade de acessos:

    -   **Customers**: Restritos a gerenciar exclusivamente seus próprios pedidos via escopo global de usuário.

    -   **Administrators**: Atuam como auditores e aprovadores com visibilidade total, porém proibidos de criar ou editar pedidos para si mesmos, garantindo conformidade com regras de auditoria.


9. **Observabilidade**: Implementação de um `LoggerService` centralizado para rastrear fluxos críticos e tentativas de acesso negado.
10. **Processamento Assíncrono**: As notificações de status utilizam **Redis** e **Laravel Queues** para garantir que o tempo de resposta da API não seja afetado pelo envio de e-mails/notificações, realizando novas tentativas em casos de falhas.


## 🛡️ Qualidade de Código e CI/CD

Para garantir a confiabilidade exigida, o repositório conta com:

* **Husky & Git Hooks**: Impedem commits que quebrem o padrão de código ou que quebrem testes.
* **Laravel Pint**: Garantia de estilo de código (PSR-12).
* **GitHub Workflows**: Cada Push ou Pull Request executa automaticamente a análise de Lint e toda a suíte de testes em um ambiente isolado.  
  

