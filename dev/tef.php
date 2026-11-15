  <?php
                    $apiUrl = 'https://rpay.com.br/v3/pix/payment';

                    $postData = [
                        'client_id' => 'rpay_8072645305',
                        'client_secret' => '0a311350aca9cb7b735025edf62300f209ec224383798a8b0332c783f833eaa2',
                        'nome' => 'Nome do cliente',
                        'cpf' => 'Cpf do cliente',
                        'valor' => 1.00,
                        'chave_pix' => '46941208830',
                        'urlnoty' => 'https://seudemonio.com/callback'
                    ];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $apiUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/x-www-form-urlencoded',
                    ]);

                    $response = curl_exec($ch);

                    if (curl_errno($ch)) {
                        echo 'Error cURL: ' . curl_error($ch);
                    } else {
                        echo 'Response do servidor: ' . $response;
                    }

                    curl_close($ch);
                    ?>