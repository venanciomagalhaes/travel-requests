
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


Esta versão final do guia de execução está otimizada para ser prática, visual e direta. Removi o código bruto do Seeder (que deve ficar apenas nos arquivos `.php`) e transformei em uma tabela de credenciais clara para o avaliador, além de ajustar as portas conforme sua indicação (`3101`).

---

## 🚀 Como Executar

O projeto está totalmente conteinerizado com **Docker Compose**, garantindo paridade total entre os ambientes de desenvolvimento e produção.

### 1. Clonar e Configurar

```bash
git clone https://github.com/venanciomagalhaes/travel-requests.git
cd travel-requests
cp .env.example .env

```

As notificações são processadas em background via Redis. Por padrão os envios serão feitos para uma caixa privada do mailtrap. Caso deseje visualizar as notificações, crie uma conta e obtenha suas credenciais https://mailtrap.io/ em seguida preencha as variáveis de ambiete:


```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
```

Ou, caso não deseje obter as credenciais, utilize o sistema de log nativo. Dentro do arquivo storage/log/laravel.log será impresso o txt da notificação, para fins de conferência. Para isso, basta adicionar em seu .env:

```
MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
```


### 2. Subir o Ambiente

Este comando iniciará os containers: **Nginx**, **PHP 8.3 (App)**, **MySQL 8**, **Redis** e o **Queue Worker**.

```bash
docker compose up -d --build

```

### 3. Preparar a Aplicação

Execute o fluxo de setup automatizado:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret --force
docker compose exec app php artisan l5-swagger:generate
docker compose exec app php artisan migrate --seed

```

---

## 🔐 Credenciais para Teste (RBAC)

O banco de dados é populado com perfis distintos para validar a **Segregação de Funções (SoD)** e as permissões de cada role:

| Perfil | E-mail | Senha | Principais Permissões |
| --- | --- | --- | --- |
| **Administrador** | `admin@onfly.com.br` | `admin@onfly.com.br` | Visualiza todos os pedidos, Aprova/Cancela solicitações. |
| **Customer** | `customer@onfly.com.br` | `customer@onfly.com.br` | Cria e visualiza apenas seus próprios pedidos. |

---

## 📑 Acesso e Documentação

O microsserviço expõe os seguintes pontos de acesso:

* 🌐 **API Base:** [http://localhost:3101](http://localhost:3101)
* 📖 **Swagger (OpenAPI 3.0):** [http://localhost:3101/api/documentation](http://localhost:3101/api/documentation)

> **Dica de Teste:** No Swagger, utilize o endpoint de `login` ou de `register` para obter o Token JWT. Clique no botão **Authorize** e insira o token no formato  para habilitar as rotas protegidas.

---

## 🧪 Testes e Qualidade

A suíte de testes utiliza **Pest PHP** para validar a lógica de negócio, autenticação e autorização.

**Executar testes com relatório de cobertura:**

```bash
docker compose exec app php artisan test --coverage

```

---

### Notas Adicionais:

* **Segurança:** O Administrador possui permissão de `CHANGE_STATUS`, mas é impedido por permissionamento de criar viagens, para que a pessoa que possui capacidade de aprovação não seja capaz de criar e aprovar ao mesmo tempo.
