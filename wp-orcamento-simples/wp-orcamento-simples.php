<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// ---------------------------------------------------------
// 1. ÁREA DE ADMINISTRAÇÃO (Igual à anterior)
// ---------------------------------------------------------

function wpos_adicionar_menu() {
    add_menu_page('Configurar Orçamento', 'Calculadora Preço', 'manage_options', 'wpos-config', 'wpos_renderizar_admin', 'dashicons-calculator');
}
add_action('admin_menu', 'wpos_adicionar_menu');

function wpos_registar_settings() {
    register_setting('wpos_options_group', 'wpos_preco_metro');
}
add_action('admin_init', 'wpos_registar_settings');

function wpos_renderizar_admin() {
    ?>
    <div class="wrap">
        <h1>Configuração da Calculadora</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wpos_options_group'); ?>
            <?php do_settings_sections('wpos_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Preço por Metro Quadrado (€):</th>
                    <td>
                        <input type="number" step="0.01" name="wpos_preco_metro" value="<?php echo esc_attr(get_option('wpos_preco_metro', '10')); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// ---------------------------------------------------------
// 2. FRONTEND COM DESIGN MODERNO
// ---------------------------------------------------------

function wpos_shortcode_calculadora() {
    $preco_base = get_option('wpos_preco_metro', '10');

    ob_start(); 
    ?>
    
    <style>
        .wpos-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 30px;
            max-width: 450px;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 20px auto;
            border: 1px solid #f0f0f0;
        }

        .wpos-title {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .wpos-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .wpos-field {
            flex: 1;
        }

        .wpos-label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .wpos-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box; /* Garante que padding não quebra layout */
        }

        .wpos-input:focus {
            outline: none;
            border-color: #007cba; /* Cor azul padrão do WP */
        }

        .wpos-result-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-top: 10px;
            border: 1px dashed #ccd0d4;
        }

        .wpos-total-label {
            font-size: 0.9rem;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: block;
        }

        .wpos-total-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2ecc71; /* Verde Dinheiro */
        }
        
        .wpos-currency {
            font-size: 1.5rem;
            color: #27ae60;
        }

        /* Responsivo para telemóveis */
        @media (max-width: 480px) {
            .wpos-grid { flex-direction: column; gap: 10px; }
        }
    </style>

    <div class="wpos-card">
        <h3 class="wpos-title">Simular Orçamento</h3>
        
        <div class="wpos-grid">
            <div class="wpos-field">
                <label class="wpos-label">Largura (m)</label>
                <input type="number" class="wpos-input" id="wpos-width" step="0.01" placeholder="0.00">
            </div>

            <div class="wpos-field">
                <label class="wpos-label">Altura (m)</label>
                <input type="number" class="wpos-input" id="wpos-height" step="0.01" placeholder="0.00">
            </div>
        </div>

        <div class="wpos-result-box">
            <span class="wpos-total-label">Estimativa Total</span>
            <span class="wpos-total-value" id="wpos-total">0.00</span>
            <span class="wpos-currency">€</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pricePerUnit = <?php echo json_encode((float)$preco_base); ?>;
            const widthInput = document.getElementById('wpos-width');
            const heightInput = document.getElementById('wpos-height');
            const totalSpan = document.getElementById('wpos-total');

            function calculate() {
                const width = parseFloat(widthInput.value) || 0;
                const height = parseFloat(heightInput.value) || 0;
                
                // Animação simples: Se o valor for > 0, muda a cor
                let total = (width * height * pricePerUnit).toFixed(2);
                
                totalSpan.innerText = total;
            }

            widthInput.addEventListener('input', calculate);
            heightInput.addEventListener('input', calculate);
        });
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('calculadora_orcamento', 'wpos_shortcode_calculadora');
