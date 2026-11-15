<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));

$replacements = [
    'havand then' => 'have an',
    'Perfil' => 'Profile',
    'Gerencie suas informações pessoais e documentação' => 'Manage your personal information and documentation',
    'Nome completo:' => 'Full name:',
    'Data de Nascimento:' => 'Date of Birth:',
    'Endereço:' => 'Address:',
    'Telefone:' => 'Mobile:',
    'Status da Conta:' => 'Account Status:',
    'Documentação' => 'Documentation',
    'Envie seus documentos para verificação' => 'Submit your documents for verification',
    'Frente do Documento' => 'Document Front',
    'Verso do Documento' => 'Document Back',
    'Comprovante de Residência' => 'Proof of Address',
    'Enviar' => 'Submit',
    'Configurações' => 'Settings',
    'Alterar Senha' => 'Change Password',
    'Senha Atual:' => 'Current Password:',
    'Nova Senha:' => 'New Password:',
    'Confirmar Nova Senha:' => 'Confirm New Password:',
    'Salvar Alterações' => 'Save Changes',
    'Chaves API' => 'API Keys',
    'Gerenciar chaves de acesso' => 'Manage access keys',
    'Sua Chave Pública' => 'Your Public Key',
    'Sua Chave Privada' => 'Your Private Key',
    'Gerar Novas Chaves' => 'Generate New Keys',
    'Copiar' => 'Copy',
    'Transferências' => 'Transfers',
    'Adicionar Dinheiro' => 'Add Money',
    'Retirada' => 'Withdrawal',
    'Prêmios' => 'Rewards',
    'Reivindicar Prêmio' => 'Claim Reward'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        if (strpos($file->getPathname(), 'vendor') !== false) continue; // Skip vendor
        if ($file->getFilename() === 'fix_corruption.php') continue;
        
        $content = file_get_contents($file->getPathname());
        $newContent = strtr($content, $replacements);
        
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Fixed: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done fixing!\n";
?>
