# Gitflow do Projeto

Este documento descreve o fluxo de trabalho Git (Gitflow) utilizado no desenvolvimento do projeto Fynn. A adoção deste modelo visa organizar o desenvolvimento, isolar novas funcionalidades e garantir a estabilidade das versões em produção.

## Ramificações (Branches)

O Gitflow se baseia em duas branches principais e três branches de suporte.

### Branches Principais

1.  **`main`**
    -   **Propósito:** Representa o código em produção. Todo commit na `main` é uma nova versão estável.
    -   **Regras:**
        -   Ninguém faz commit diretamente na `main`.
        -   Apenas branches `release` ou `hotfix` podem ser mescladas (merged) na `main`.
        -   Cada merge na `main` deve ter uma tag de versão (ex: `v1.0.0`).

2.  **`dev`**
    -   **Propósito:** É a branch de desenvolvimento principal. Contém o código mais recente com as novas funcionalidades já concluídas e testadas.
    -   **Regras:**
        -   É a branch base para criar novas `feature branches`.
        -   Quando uma `feature` é concluída, ela é mesclada de volta na `dev`.

### Branches de Suporte

1.  **`feature/*`** (Ex: `feature/nova-tela-login`)
    -   **Propósito:** Desenvolver novas funcionalidades.
    -   **Ciclo de Vida:**
        1.  Criada a partir da `dev`.
        2.  O desenvolvimento da funcionalidade ocorre nesta branch.
        3.  Ao concluir, é mesclada de volta na `dev`.
        4.  Após o merge, a branch de feature é deletada.

2.  **`hotfix/*`** (Ex: `hotfix/correcao-bug-critico`)
    -   **Propósito:** Corrigir bugs críticos em produção de forma rápida.
    -   **Ciclo de Vida:**
        1.  Criada a partir da `main`.
        2.  A correção é implementada e testada.
        3.  Após a conclusão, a branch `hotfix` é mesclada na `main` e na `dev`.
        4.  A branch `main` recebe uma nova tag de versão (ex: `v1.0.1`).
        5.  A branch `hotfix` é deletada.

## Fluxo de Trabalho Padrão (Nova Funcionalidade)

1.  **Sincronize sua branch `dev` local:**
    ```bash
    git checkout dev
    git pull origin dev
    ```

2.  **Crie uma nova branch de feature:**
    ```bash
    git checkout -b feature/nome-da-sua-feature
    ```

3.  **Desenvolva e faça commits:**
    ```bash
    // ... faz alterações no código ...
    git add .
    git commit -m "feat: implementa a funcionalidade X"
    ```

4.  **Envie a feature para o repositório remoto:**
    ```bash
    git push origin feature/nome-da-sua-feature
    ```

5.  **Abra um Pull Request (PR):**
    -   No GitHub (ou plataforma similar), abra um PR da sua `feature/*` para a `dev`.
    -   Aguarde a revisão e aprovação.

6.  **Mescle o PR:**
    -   Após a aprovação, mescle o PR na `dev`.
    -   Delete a branch da feature.
