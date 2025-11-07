# Cielo 3.0 WooCommerce Payment Gateway

Esse projeto trata-se de um plugin de WordPress para habilitar o WooCommerce para pagamentos via cartão de crédito com a API Cielo 3.0 Ecommerce.

## Capacidades

O plugin permite configurações personalizadas para necessidades diversas, tais quais:

* Ativo ou inativo

* Nome de exibição do método de pagamento

* Ativar teste (padrão desligado)

* Merchant ID / Merchant Key (Produção) (credenciais Cielo)

* Merchant ID / Merchant Key (Teste) (credenciais Cielo)

* Código do estabelecimento (opcional)

* Descrição na fatura

* Número máximo de parcelas (máximo de 12x)

* Valor mínimo da parcela (mínimo R$ 5,00)

* Ativar Debug (padrão desligado)

## Requisitos

* WordPress 5.0+
* WooCommerce 9.4+
* Conta ativa Cielo

## Primeiros passos

1. Instale o plugin via .zip em um ambiente WordPress.

2. Abre o menu WooCommerce / Pagamentos / Cielo eCommerce 3.0 Gateway / Concluir configuração 

3. Configure as variáveis MerchantId, MerchantKey e defina o ambiente desejado.

4. Coloque um produto no carrinho e avance ao checkout

5. Preencha os dados do cartão e finalize a compra.

## Roadmap

### Integração Pix

* Gerar QR Code
* Adicionar configuração de limite de expiração do QR Code

### Cartão de débito

* Adicionar seletor de método de pagamento
* Adicionar condicional para esconder parcelas caso seletor seja cartão de débito
