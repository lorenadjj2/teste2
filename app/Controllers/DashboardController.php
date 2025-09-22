<?php
// ProjetoGES_MVC/app/Controllers/DashboardController.php

namespace App\Controllers;

use App\Core\Session;
use App\Core\Controller; // Certifique-se de que a classe Session está sendo usada

class DashboardController extends Controller
{
    public function index()
    {
        error_log("[" . date('d-M-Y H:i:s e') . "] DEBUG: DashboardController@index foi chamado.");

        // Redireciona para o login se o usuário não estiver logado
        if (!Session::isLoggedIn()) {
            error_log("[" . date('d-M-Y H:i:s e') . "] DEBUG: DashboardController: Usuário não logado, redirecionando para /login.");
            Session::set('login_erro', 'Você precisa estar logado para acessar o dashboard.');
            header('Location: /login');
            exit();
        }

        $userEmail = Session::get('email');
        $userProfile = Session::get('perfil'); // Pega o perfil da sessão

        // --- Lógica para o Menu Dinâmico ---
        // Chama o método para obter os itens de menu com a estrutura de sub-itens
        $menuItems = $this->getDynamicMenu($userProfile);
        // ------------------------------------

        $data = [
            'userEmail' => $userEmail,
            'userProfile' => $userProfile,
            'menuItems' => $menuItems // Passa os itens do menu para a View
        ];

        $this->view('dashboard/index', $data); // Renderiza a view do dashboard, que incluirá o header/footer se não for login.
    }

    /**
     * Retorna os itens de menu permitidos para um dado perfil, incluindo submenus.
     * Esta lógica pode ser movida para um Helper/Service mais tarde para maior organização.
     * @param string $profile O perfil do usuário (ex: 'admin', 'professor')
     * @return array Array de itens de menu [ 'text' => 'Texto', 'route' => '/rota', 'sub_items' => [] ]
     */
    private function getDynamicMenu(string $profile): array
    {
        // Define todos os menus em uma única estrutura
        $allMenuItems = [
            //--------------------------------------------------------------------------------- MENU COMPLETO PARA A ADMIN------------------------------------------------------------------------------------
           'admin' => [
                // Painel Principal
                [
                    'text' => 'Dashboard',
                    'route' => '/dashboard',
                    'sub_items' => []
                ],

                // Gestão de Acesso
                [
                    'text' => 'Usuários',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Usuário', 'route' => '/admin/users'],
                        ['text' => 'Cadastrar Usuário', 'route' => '/admin/users/register'],
                        ['text' => 'Pesquisar Usuário', 'route' => '/admin/users/search'],
                    ]
                ],

                // Pessoas da Instituição
                [
                    'text' => 'Aluno',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Aluno', 'route' => '/admin/alunos'],
                        ['text' => 'Cadastrar Aluno', 'route' => '/admin/alunos/register'],
                        ['text' => 'Pesquisar Aluno', 'route' => '/admin/alunos/search'],
                    ]
                ],
                [
                    'text' => 'Professor',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Professor', 'route' => '/admin/professores'],
                        ['text' => 'Cadastrar Novo Professor', 'route' => '/admin/professores/register'],
                        ['text' => 'Pesquisar Professor', 'route' => '/admin/professores/search'],
                        ['text' => 'Vincular Professor ao Curso', 'route' => '/admin/professor_curso/atribuir'],                       // ↪ Cadastro de vínculos
                        ['text' => 'Listar Atribuições de Curso', 'route' => '/admin/professor_curso/cursos_atribuidas'],    // ↪ Atribuições feitas
                        ['text' => 'Consultar Cursos por Professor', 'route' => '/admin/professor_curso/consulta_professor'], // ✅ NOVO LINK
                        ['text' => 'Vincular Disciplina ao Professor', 'route' => '/admin/professor_disciplinas/atribuir'],
                        ['text' => 'Listar Atribuições de Disciplinas', 'route' => '/admin/professor_disciplinas/disciplinas_atribuidas']
                    ]

                ],

                // Estrutura Curricular
                [
                    'text' => 'Curso',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Curso', 'route' => '/admin/cursos'],
                        ['text' => 'Cadastrar Curso', 'route' => '/admin/cursos/register'],                        
                        ['text' => 'Pesquisar Curso', 'route' => '/admin/cursos/search'],
                    ]
                ],
                [
                    'text' => 'Disciplina',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Disciplina', 'route' => '/admin/disciplinas'],
                        ['text' => 'Cadastrar Disciplina', 'route' => '/admin/disciplinas/register'],
                        ['text' => 'Consultar Disciplina', 'route' => '/admin/disciplinas/consult'],
                        ['text' => 'Pesquisar Disciplina', 'route' => '/admin/disciplinas/search'],
                        ['text' => 'Vincular ao Curso', 'route' => '/admin/disciplinadocurso/register'],
                        ['text' => 'Visualizar Vinculadas', 'route' => '/admin/disciplinadocurso'],
                    ]
                ],
                [
                    'text' => 'Plano de Curso',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Plano de Curso', 'route' => '/admin/planodecurso'],
                        ['text' => 'Cadastrar Plano de Curso', 'route' => '/admin/planodecurso/register'],                        
                        ['text' => 'Pesquisar Plano de Curso', 'route' => '/admin/planodecurso/search'],
                    ]
                ],
                [
                    'text' => 'Plano de Aula',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Plano de Aula', 'route' => '/admin/planodeaula/'],
                        ['text' => 'Cadastrar Plano de Aula', 'route' => '/admin/planodeaula/register'],                        
                        ['text' => 'Pesquisar Plano de Aula', 'route' => '/admin/planodeaula/search'],
                    ]
                ],

                // Vida Acadêmica
                [
                    'text' => 'Matrícula',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Matrícula', 'route' => '/admin/matriculas'],
                        ['text' => 'Cadastrar Matrícula', 'route' => '/admin/matriculas/register'],
                        ['text' => 'Pesquisar Matrícula', 'route' => '/admin/matriculas/search'],
                        ['text' => 'Renovar Matrícula', 'route' => '/admin/matriculas/renovar'], // Nova rota para renovação de matrícula
                         // 👇 NOVAS ROTAS RELACIONADAS À MATRÍCULA EM DISCIPLINAS
                        ['text' => 'Gerenciar Matrículas em Disciplinas', 'route' => '/admin/matriculadisciplinas'],
                        ['text' => 'Cadastrar Matrícula em Disciplina', 'route' => '/admin/matriculadisciplinas/register'],
                    ]
                ],
                [
                    'text' => 'Turmas',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Turmas', 'route' => '/admin/turmas'],
                        ['text' => 'Cadastrar Turmas', 'route' => '/admin/turmas/register-new'],
                        ['text' => 'Gerar Turmas', 'route' => '/admin/turmas/generate'],
                        ['text' => 'Consultar Turmas', 'route' => '/admin/turmas/consult'],
                    ]
                ],
                // Aula & Avaliação
                [
                    'text' => 'Gestão de Aulas',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Registrar Nova Aula', 'route' => '/admin/aulas'],
                        ['text' => 'Listar Aulas', 'route' => '/admin/aulas/lista'],
                        ['text' => 'Consultar Frequência', 'route' => '/admin/aulas/frequencia/consultar'],
                        ['text' => 'Lançar Notas', 'route' => '/admin/notas'],
                        ['text' => 'Gerenciar Diários', 'route' => '/admin/diario'],
                        ['text' => 'Gerar Diário Excel', 'route' => '/admin/diario/gerar_excel_form'],
                        ['text' => 'Informações Complementares', 'route' => '/admin/informacoes_complementares'],
                        ['text' => 'Cadastrar Informação Complementar', 'route' => '/admin/informacoes_complementares/register'],
                        ['text' => 'Avaliações Diagnósticas', 'route' => '/admin/avadiagno'],
                        ['text' => 'Cadastrar Avaliação Diagnóstica', 'route' => '/admin/avadiagno/register'],
                    ]

                ],
                [
                    'text' => 'Atividades',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Atividades', 'route' => '/admin/atividades'],
                        ['text' => 'Cadastrar Atividades', 'route' => '/admin/atividades/register'],
                        ['text' => 'Pesquisar Atividades', 'route' => '/admin/atividades/search'],
                    ]
                ],

                // Documentos Finais
                [
                    'text' => 'Documentos Finais',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerar Ata de Conselho', 'route' => '/admin/ata/gerar'],
                        // Futuro: Histórico Escolar, Certificados, etc.
                    ]
                ],
                [
                    'text' => 'Gerar Documentos',
                    'route' => '/admin/documentos',
                    'sub_items' => []
                ],               
            ],
            //--------------------------------------------------------------------------------- MENU COMPLETO PARA A PROFESSOR------------------------------------------------------------------------------------
            'professor' => [
                ['text' => 'Dashboard', 'route' => '/dashboard', 'sub_items' => []],

                // PLANEJAMENTO
                [
                    'text' => 'Plano de Aula',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Planos', 'route' => '/professor/planodeaula'],
                        ['text' => 'Cadastrar Plano', 'route' => '/professor/planodeaula/register'],
                    ]
                ],

                // ATIVIDADES
                [
                    'text' => 'Atividades',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Atividades', 'route' => '/professor/atividades'],
                        ['text' => 'Cadastrar Atividades', 'route' => '/professor/atividades/register'],
                        ['text' => 'Consultar Atividades', 'route' => '/professor/atividades/consult'],
                        ['text' => 'Correção em Lote', 'route' => '/professor/atividades/correcao_lote'], // possível nova função
                    ]
                ],

                // AULAS E DIÁRIO
                [
                    'text' => 'Gestão de Aulas',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Registrar Nova Aula', 'route' => '/professor/aulas'],
                        ['text' => 'Listar Aulas', 'route' => '/professor/aulas/lista'],
                        ['text' => 'Consultar Frequência', 'route' => '/professor/aulas/frequencia/consultar'],
                        ['text' => 'Lançar Notas', 'route' => '/professor/notas'],
                        ['text' => 'Diário Eletrônico', 'route' => '/professor/diario'],
                        ['text' => 'Informações Complementares', 'route' => '/professor/informacoes_complementares'], // alinhado com o menu do admin
                        ['text' => 'Cadastrar Informação Complementar', 'route' => '/professor/informacoes_complementares/register'],
                        ['text' => 'Histórico de Edições', 'route' => '/professor/diario/historico'], // sugestão para rastreabilidade
                    ]
                ],

                // APOIO AO PROFESSOR
                [
                    'text' => 'Meus Cursos e Turmas',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Minhas Turmas', 'route' => '/professor/turmas'],
                        ['text' => 'Meus Cursos', 'route' => '/professor/cursos'],
                        ['text' => 'Calendário Acadêmico', 'route' => '/professor/calendario'], // útil para planejamento
                    ]
                ],

                // COMUNICAÇÃO
                [
                    'text' => 'Comunicação',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Enviar Avisos', 'route' => '/professor/avisos/enviar'],
                        ['text' => 'Ver Retornos dos Alunos', 'route' => '/professor/avisos/respostas'],
                    ]
                ],
            ], 
            //--------------------------------------------------------------------------------- MENU COMPLETO PARA O COORDENADOR------------------------------------------------------------------------------------
           'coordenador' => [
                ['text' => 'Dashboard', 'route' => '/dashboard', 'sub_items' => []],

                // GESTÃO CURRICULAR
                [
                    'text' => 'Disciplinas e Cursos',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Listar Disciplinas', 'route' => '/coordenador/disciplinas'],
                        ['text' => 'Cadastrar Disciplina', 'route' => '/coordenador/disciplinas/register'],
                        ['text' => 'Listar Planos de Curso', 'route' => '/coordenador/planodecurso'],
                        ['text' => 'Cadastrar Plano de Curso', 'route' => '/coordenador/planodecurso/register'],
                    ]
                ],

                // ATRIBUIÇÕES DOCENTES
                [
                    'text' => 'Atribuição de Professores',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Vincular Disciplina ao Professor', 'route' => '/coordenador/professor_disciplinas'],
                        ['text' => 'Disciplinas Atribuídas', 'route' => '/coordenador/professor_disciplinas/disciplinas_atribuidas'],
                        ['text' => 'Cadastrar Professor no Curso', 'route' => '/coordenador/professor_curso'],
                        ['text' => 'Cursos Atribuídos', 'route' => '/coordenador/professor_curso/cursos_atribuidos'],
                    ]
                ],

                // ACOMPANHAMENTO DOCENTE
                [
                    'text' => 'Planos de Aula',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Listar Planos', 'route' => '/coordenador/planodeaula'],
                        ['text' => 'Cadastrar Novo Plano', 'route' => '/coordenador/planodeaula/register'],
                        ['text' => 'Avaliar Planos Submetidos', 'route' => '/coordenador/planodeaula/avaliar'], // sugestão extra
                    ]
                ],

                // TURMAS E ESTUDANTES
                [
                    'text' => 'Gestão de Turmas',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Listar Turmas', 'route' => '/coordenador/turmas'],
                        ['text' => 'Cadastrar Nova Turma', 'route' => '/coordenador/turmas/register-new'],
                        ['text' => 'Gerar Turmas (Atribuir Alunos)', 'route' => '/coordenador/turmas/generate'],
                        ['text' => 'Consultar Alunos por Turma', 'route' => '/coordenador/turmas/consult'],
                    ]
                ],

                // AULAS E DIÁRIO
                [
                    'text' => 'Gestão de Aulas e Diários',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Registrar Nova Aula', 'route' => '/coordenador/aulas'],
                        ['text' => 'Listar Aulas', 'route' => '/coordenador/aulas/lista'],
                        ['text' => 'Consultar Frequência', 'route' => '/coordenador/aulas/frequencia/consultar'],
                        ['text' => 'Lançar Notas', 'route' => '/coordenador/notas'],
                        ['text' => 'Gerenciar Diários', 'route' => '/coordenador/diario'],
                        ['text' => 'Informações Complementares', 'route' => '/coordenador/informacoes_complementares'], // manter alinhado com os outros perfis
                        ['text' => 'Cadastrar Informação Complementar', 'route' => '/coordenador/informacoes_complementares/register'],
                    ]
                ],

                // ENCERRAMENTO DO PERÍODO
                [
                    'text' => 'Documentos Finais',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerar Ata de Conselho', 'route' => '/coordenador/ata/gerar'],
                        ['text' => 'Histórico Escolar', 'route' => '/coordenador/historico'], // previsão futura
                    ]
                ],

                // EMISSÃO E ANÁLISE DE DOCUMENTOS
                [
                    'text' => 'Gerar Documentos e Relatórios',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerar Documentos', 'route' => '/coordenador/documentos'],
                        ['text' => 'Relatórios Gerenciais', 'route' => '/coordenador/relatorios'],
                    ]
                    ],
                ],
                //--------------------------------------------------------------------------------- MENU COMPLETO PARA A SECRETARIA------------------------------------------------------------------------------------
             'secretaria' => [
                ['text' => 'Dashboard', 'route' => '/dashboard', 'sub_items' => []],

                // GESTÃO ACADÊMICA
                [
                    'text' => 'Alunos',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Alunos', 'route' => '/secretaria/alunos'],
                        ['text' => 'Cadastrar Aluno', 'route' => '/secretaria/alunos/register'],
                        ['text' => 'Pesquisar Aluno', 'route' => '/secretaria/alunos/search'],
                        ['text' => 'Histórico Escolar', 'route' => '/secretaria/alunos/historico'], // sugestão futura
                    ]
                ],

                [
                    'text' => 'Professores',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Professores', 'route' => '/secretaria/professores'],
                        ['text' => 'Cadastrar Professor', 'route' => '/secretaria/professores/register'],
                        ['text' => 'Atribuir Disciplina/Turma', 'route' => '/secretaria/professores/atribuir-disciplina'],
                    ]
                ],

                [
                    'text' => 'Matrículas',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Matrículas', 'route' => '/secretaria/matriculas'],
                        ['text' => 'Cadastrar Matrícula', 'route' => '/secretaria/matriculas/register'],
                        ['text' => 'Logs de Matrícula', 'route' => '/secretaria/matriculas/logs'], // sugestão futura
                    ]
                ],

                [
                    'text' => 'Cursos e Turmas',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Cursos', 'route' => '/secretaria/cursos'],
                        ['text' => 'Cadastrar Curso', 'route' => '/secretaria/cursos/register'],
                        ['text' => 'Gerenciar Turmas', 'route' => '/secretaria/turmas'],
                        ['text' => 'Cadastrar Nova Turma', 'route' => '/secretaria/turmas/register-new'],
                        ['text' => 'Gerar Turmas (Atribuir Alunos)', 'route' => '/secretaria/turmas/generate'],
                        ['text' => 'Consultar Turmas', 'route' => '/secretaria/turmas/consult'],
                    ]
                ],

                // DIÁRIO E DOCUMENTAÇÃO ESCOLAR
                [
                    'text' => 'Diários e Documentos',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Gerenciar Diários', 'route' => '/secretaria/diario'],
                        ['text' => 'Gerar Documentos', 'route' => '/secretaria/documentos'],
                        ['text' => 'Emitir Declarações e Comprovantes', 'route' => '/secretaria/documentos/declaracoes'], // sugestão extra
                    ]
                ],

                // COMUNICAÇÃO
                [
                    'text' => 'Comunicação Interna',
                    'route' => '#',
                    'sub_items' => [
                        ['text' => 'Enviar Avisos', 'route' => '/secretaria/avisos/enviar'],
                        ['text' => 'Visualizar Retornos', 'route' => '/secretaria/avisos/respostas'],
                    ]
                ],
            ],
                       
        ];

        // Retorna o menu correspondente ao perfil
        return $allMenuItems[$profile] ?? [];
    }
}
