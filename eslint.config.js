import tseslint from '@typescript-eslint/eslint-plugin';
import tsParser from '@typescript-eslint/parser';
import prettier from 'eslint-config-prettier';
import importPlugin from 'eslint-plugin-import';
import simpleImportSort from 'eslint-plugin-simple-import-sort';
import vue from 'eslint-plugin-vue';

export default [
    // Base for Vue SFC parsing
    {
        files: ['**/*.{js,jsx,ts,tsx,vue}'],
        languageOptions: {
            parser: vue.parser,
            parserOptions: {
                parser: tsParser, // <script lang="ts">
                ecmaVersion: 'latest',
                sourceType: 'module',
                extraFileExtensions: ['.vue'],
            },
        },
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
            'tailwind.config.js',
            'resources/js/components/ui/*',
        ],
        plugins: {
            vue,
            '@typescript-eslint': tseslint,
            import: importPlugin,
            'simple-import-sort': simpleImportSort,
        },
        rules: {
            // --- Vue ---
            'vue/multi-word-component-names': 'off',

            // Self-closing: always for void/normal components when empty
            'vue/html-self-closing': [
                'error',
                {
                    html: {
                        void: 'always',
                        normal: 'always',
                        component: 'always',
                    },
                    svg: 'always',
                    math: 'always',
                },
            ],

            // --- TS relaxations (match your sample) ---
            '@typescript-eslint/no-explicit-any': 'off',

            // --- Imports: modules first, then local; alpha inside groups ---
            // Use ONE sorter (disable TS "organize imports" in your editor to avoid clashes)
            'simple-import-sort/imports': [
                'error',
                {
                    groups: [
                        // 1. Node builtins and external packages
                        [
                            '^node:',
                            '^(assert|buffer|child_process|crypto|fs|http|https|os|path|stream|url)(/.*)?$',
                            '^@?\\w',
                        ],
                        // 2. Side effect imports
                        ['^\\u0000'],
                        // 3. Internal aliases (e.g. @/, ~/) – adjust to your aliases
                        ['^@/(.*)$', '^~/(.*)$'],
                        // 4. Parent/relative
                        ['^\\.\\.(?!/?$)', '^\\.\\./?$'],
                        ['^\\.(?!/?$)', '^\\./?$'],
                        // 5. Style files
                        ['^.+\\.s?css$'],
                    ],
                },
            ],
            'simple-import-sort/exports': 'error',

            // Helps detect unresolved/duplicate imports (not formatting)
            'import/no-duplicates': 'error',
            'import/order': 'off', // keep off to avoid conflicting with simple-import-sort
        },
    },

    // Turn off all stylistic rules that Prettier handles
    prettier,
];
