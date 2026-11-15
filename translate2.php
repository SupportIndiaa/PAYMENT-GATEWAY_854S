<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));

$replacements = [
    // index.php
    'Faturamento Insuficiente' => 'Insufficient Revenue',
    'Entendi' => 'Understood',
    'Total Transações' => 'Total Transactions',
    'Vendas nos Últimos Dias' => 'Sales in Last Days',
    '7 Dias' => '7 Days',
    '30 Dias' => '30 Days',
    'Hoje' => 'Today',
    'Atividades Recentes' => 'Recent Activities',
    'Nenhuma transação encontrada' => 'No transaction found',
    'Quando você receber transações, elas aparecerão aqui' => 'When you receive transactions, they will appear here',
    'Resumo Financeiro' => 'Financial Summary',
    'Tipo' => 'Type',
    'Este Mês' => 'This Month',
    'Mês Anterior' => 'Last Month',
    
    // general
    'Termos e Condições' => 'Terms and Conditions',
    'Política de Privacidade' => 'Privacy Policy',
    'Aceitação dos Termos' => 'Acceptance of Terms',
    'Descrição dos Serviços' => 'Description of Services',
    'Responsabilidades do Usuário' => 'User Responsibilities',
    'Taxas e Pagamentos' => 'Fees and Payments',
    'Limitações de Responsabilidade' => 'Limitations of Liability',
    'Modificações dos Termos' => 'Modifications of Terms',
    'Coleta de Informações' => 'Information Collection',
    'Uso das Informações' => 'Use of Information',
    'Compartilhamento de Dados' => 'Data Sharing',
    'Segurança dos Dados' => 'Data Security',
    'Seus Direitos' => 'Your Rights',
    'Retenção de Dados' => 'Data Retention',
    'Contato' => 'Contact',
    
    // Profile
    'Settings da Conta' => 'Account Settings',
    'Informações Pessoais' => 'Personal Information',
    'Segurança' => 'Security',
    'Notificações' => 'Notifications',
    'Salvar' => 'Save',
    'Cancelar' => 'Cancel',
    'Sim' => 'Yes',
    'Não' => 'No',
    'Sucesso' => 'Success',
    'Erro' => 'Error',
    'Aviso' => 'Warning',
    'Atenção' => 'Attention',
    'Por favor, aguarde...' => 'Please wait...',
    'Carregando...' => 'Loading...',
    
    // Register specific
    'Criar conta' => 'Create account',
    'Eu li e concordo com os' => 'I have read and agree to the',
    'e a' => 'and the',
    'Já tem uma conta?' => 'Already have an account?',
    'Fazer login' => 'Login',
    'Nome de usuário' => 'Username',
    
    // Admin / Backoffice
    'Usuários' => 'Users',
    'Transações' => 'Transactions',
    'Settings do Sistema' => 'System Settings',
    'Sair' => 'Logout'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        // Skip this script itself
        if ($file->getFilename() === 'translate2.php') continue;
        
        $content = file_get_contents($file->getPathname());
        
        // Fix encoding for replace
        $newContent = strtr($content, $replacements);
        
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Translated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done translating!\n";
?>
