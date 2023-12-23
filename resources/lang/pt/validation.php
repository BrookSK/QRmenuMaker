<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Linhas de linguagem de validação
    |--------------------------------------------------------------------------
    |
    | As linhas de idioma a seguir contêm as mensagens de erro padrão usadas por
    | a classe validadora. Algumas dessas regras têm múltiplas versões, como
    | as regras de tamanho. Sinta-se à vontade para ajustar cada uma dessas mensagens aqui.
    |
    */

    'accepted' => 'O :attribute deve ser aceito.', 
    'active_url' => 'O :attribute não é uma URL válida.', 
    'after' => 'O :attribute deve ser uma data posterior a :date .', 
    'after_or_equal' => 'O :attribute deve ser uma data posterior ou igual a :date.', 
    'alpha' => 'O :attribute só pode conter letras.', 
    'alpha_dash' => 'O :attribute pode conter apenas letras, números, travessões e sublinhados.', 
    'alpha_num' => 'O :attribute pode conter apenas letras e números.', 
    'array' => 'O :attribute deve ser um array.', 
    'before' => 'O :attribute deve ser uma data anterior a :date.', 
    'before_or_equal' => 'O :attribute deve ser uma data anterior ou igual a :date.', 
    'between' => [ 
        'numeric' => ' O :attribute deve estar entre :min e :max.', 
        'file' => 'O :attribute deve estar entre :min e :max kilobytes.', 
        'string' => 'O :attribute deve estar entre :min e :max :max caracteres.', 
        'array' => 'O :attribute deve ter entre :min e :max itens.', 
    ], 
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.', 
    'confirmed' => 'A confirmação de :attribute não corresponde.',
    'date' => 'O :attribute não é uma data válida.', 
    'date_equals' => 'O :attribute deve ser uma data igual a :date.', 
    'date_format' => 'O :attribute não corresponde ao formato :format.', 
    'different' => 'O :attribute e :other devem ser diferentes.',
    'digits' => 'O :attribute deve ser :digits dígitos.', 
    'digits_between' => 'O :attribute deve estar entre :min e :max dígitos.', 
    'dimensions' => 'O :attribute tem dimensões de imagem inválidas.',
    'distinct' => 'O campo :attribute tem um valor duplicado.',
    'email' => 'O :attribute deve ser um endereço de e-mail válido.',
    'ends_with' => 'O :attribute deve terminar com um dos seguintes: :values.',
    'exists' => 'O atributo selecionado é inválido.',
    'file' => 'O :attribute deve ser um arquivo.',
    'filled' => 'O campo :attribute deve ter um valor.',
    'gt' => [
        'numeric' => 'O :attribute deve ser maior que :value.',
        'file' => 'O :attribute deve ser maior que :value kilobytes.',
        'string' => 'O :attribute deve ser maior que :value caracteres.',
        'array' => 'O :attribute deve ter mais que :value itens.',
    ],
    'gte' => [
        'numeric' => 'O :attribute deve ser maior ou igual a :value.',
        'file' => 'O :attribute deve ser maior ou igual a :value kilobytes.',
        'string' => 'O :attribute deve ser maior ou igual a :value caracteres.',
        'array' => 'O :attribute deve ter :value itens ou mais.',
    ],
    'image' => 'O :attribute deve ser uma imagem.',
    'in' => 'O atributo selecionado é inválido.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'integer' => 'O :attribute deve ser um número inteiro.',
    'ip' => 'O :attribute deve ser um endereço IP válido.',
    'ipv4' => 'O :attribute deve ser um endereço IPv4 válido.',
    'ipv6' => 'O :attribute deve ser um endereço IPv6 válido.',
    'json' => 'O :attribute deve ser uma string JSON válida.',
    'lt' => [
        'numeric' => 'O :attribute deve ser menor que :value.',
        'file' => 'O :attribute deve ser menor que :value kilobytes.',
        'string' => 'O :attribute deve ter menos que :value caracteres.',
        'array' => 'O :attribute deve ter menos que :value itens.',
    ],
    'lte' => [
        'numeric' => 'O :attribute deve ser menor ou igual a :value.',
        'file' => 'O :attribute deve ser menor ou igual a :value kilobytes.',
        'string' => 'O :attribute deve ser menor ou igual a :value caracteres.',
        'array' => 'O :attribute não deve ter mais que :value itens.',
    ],
    'max' => [
        'numeric' => 'O :attribute não pode ser maior que :max.',
        'file' => 'O :attribute não pode ser maior que :max kilobytes.',
        'string' => 'O :attribute não pode ser maior que :max caracteres.',
        'array' => 'O :attribute não pode ter mais que :max itens.',
    ],
    'mimes' => 'O :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'O :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'O :attribute deve ser pelo menos :min.',
        'file' => 'O :attribute deve ter pelo menos :min kilobytes.',
        'string' => 'O :attribute deve ter pelo menos :min caracteres.',
        'array' => 'O :attribute deve ter pelo menos :min itens.',
    ],
    'multiple_of' => 'O :attribute deve ser um múltiplo de :value',
    'not_in' => 'O :attribute selecionado é inválido.',
    'not_regex' => 'O formato :attribute é inválido.',
    'numeric' => 'O :attribute deve ser um número.',
    'password' => 'A senha está incorreta.',
    'present' => 'O campo :attribute deve estar presente.',
    'regex' => 'O formato :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório quando :other é :value.',
    'required_unless' => 'O campo :attribute é obrigatório, a menos que :other esteja em :values.',
    'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all' => 'O campo :attribute é obrigatório quando :values estão presentes.',
    'required_without' => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :values está presente.',
    'same' => 'O :attribute e :other devem corresponder.',
    'size' => [
        'numeric' => 'O :attribute deve ser :size.',
        'file' => 'O :attribute deve ter :size kilobytes.',
        'string' => 'O :attribute deve ter :size caracteres.',
        'array' => 'O :attribute deve conter :size itens.',
    ],
    'starts_with' => 'O :attribute deve começar com um dos seguintes: :values.',
    'string' => 'O :attribute deve ser uma string.',
    'timezone' => 'O :attribute deve ser uma zona válida.',
    'unique' => 'O :attribute já foi usado.',
    'uploaded' => 'O :attribute falhou ao carregar.',
    'url' => 'O formato :attribute é inválido.',
    'uuid' => 'O :attribute deve ser um UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Linhas de linguagem de validação personalizadas
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar mensagens de validação personalizadas para atributos usando o
    | convenção "attribute.rule" para nomear as linhas. Isso torna rápido
    | especifique uma linha de idioma personalizada específica para uma determinada regra de atributo.
    |
    */

    'custom' => [
        'order_price' => [
            'min' => 'O pedido mínimo é :min. Por favor, adicione mais alguns itens no carrinho',
        ],
        'address_id' => [
            'required' => 'Por favor selecione seu endereço',
        ],
        'stripe_payment_error_action'=>[
            'required'=>'A tentativa de pagamento falhou porque é necessária uma ação adicional antes que ela possa ser concluída'
        ],
        'stripe_payment_failure'=>[
            'required'=>'A tentativa de pagamento falhou por vários outros motivos, como falta de fundos disponíveis. Por favor, verifique os dados fornecidos.'
        ],
        'paypal_payment_error_action'=>[
            'required'=>'A tentativa de pagamento falhou porque é necessária uma ação adicional antes que ela possa ser concluída'
        ],
        'general_payment_error_action'=>[
            'required'=>'A tentativa de pagamento falhou. Se você for administrador do sistema, verifique o problema no provedor de pagamento.'
        ],
        'link_payment_error_action'=>[
            'required'=>'Método de pagamento baseado em link não encontrado'
        ],
        'paypal_payment_approval_missing'=>[
            'required'=>'Não foi possível obter o link de pagamento do PayPal.'
        ],
        'mollie_error_action'=>[
            'required'=>'Erro ao obter link de pagamento.'
        ],
        'paystack_error_action'=>[
            'required'=>"Erro na comunicação com PayStack"
        ],
        'dinein_table_id'=>[
            'required'=>'Por favor selecione sua mesa',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos de validação personalizados
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de idioma são usadas para trocar nosso espaço reservado de atributo
    | com algo mais fácil de ler, como "Endereço de e-mail", em vez disso
    | de "email". Isso simplesmente nos ajuda a tornar nossa mensagem mais expressiva.
    |
    */

    'attributes' => [],

];
