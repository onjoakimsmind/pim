# LazyVim Setup for This Project

## What's Already Configured

Your project has:
- ✅ `.editorconfig` - Editor settings (indent size, line endings)
- ✅ `.prettierrc` - Prettier config with Tailwind plugin
- ✅ `eslint.config.js` - ESLint config with Vue support
- ✅ `vendor/bin/pint` - Laravel Pint for PHP formatting

## Mason Formatters Installed

- ✅ `prettier` - JavaScript/TypeScript/Vue formatter
- ✅ `eslint_d` - Fast ESLint daemon
- ✅ `php-cs-fixer` - PHP formatter (use Pint instead)

## How It Should Work

### EditorConfig
LazyVim automatically reads `.editorconfig` if you have the plugin installed. To verify:
```vim
:EditorConfigReload
```

### Format on Save (Conform.nvim)

LazyVim uses `conform.nvim` for formatting. Open a file and:
```vim
:ConformInfo
```

This should show which formatter is configured for the current filetype.

**To format manually:**
```vim
<leader>cf    " or
:Format
```

**To format on save:**
```vim
:FormatEnable
```

### Linting (nvim-lint)

LazyVim uses `nvim-lint` for linting. To check:
```vim
:LspInfo
```

Lint errors should appear in diagnostics automatically.

## Expected Behavior by File Type

| File Type | Formatter | Linter | Indent |
|-----------|-----------|--------|--------|
| `.ts`, `.js` | prettier | eslint_d | 2 spaces |
| `.vue` | prettier | eslint_d | 2 spaces |
| `.php` | pint | intelephense | 4 spaces |
| `.css` | prettier | - | 2 spaces |
| `.json` | prettier | - | 2 spaces |

## Manual Commands

```bash
# Format with Prettier (from project root)
npm run format

# Check formatting
npm run format:check

# Lint and fix with ESLint
npm run lint

# Format PHP with Pint
vendor/bin/pint
```

## Troubleshooting

### Prettier not using project config
LazyVim should automatically find `.prettierrc` in the project root. If not:
```vim
:echo expand('%:p:h')  " Check current file directory
:!npx prettier --find-config-path %  " Find config used
```

### ESLint not working
```vim
:LspLog  " Check LSP logs
:EslintFixAll  " Try manual fix
```

### Wrong indentation
Make sure EditorConfig plugin is active:
```vim
:EditorConfigReload
:set expandtab?  " Should be 'expandtab'
:set shiftwidth?  " Should be 2 for TS/Vue, 4 for PHP
```

### Format on save not working
Enable it explicitly:
```vim
:FormatEnable
```

Or add to your LazyVim config (but you said not to touch it):
```lua
vim.api.nvim_create_autocmd("BufWritePre", {
  pattern = "*",
  callback = function(args)
    require("conform").format({ bufnr = args.buf })
  end,
})
```

## Project-Specific Config

I've created `.neoconf.json` in this project which helps LazyVim LSPs find the right settings. You don't need to do anything - it's automatically loaded when you open files in this project.

## Testing

1. Open a Vue file: `nvim resources/js/admin/pages/Dashboard.vue`
2. Check formatter: `:ConformInfo`
3. Format: `<leader>cf`
4. Check indentation: Should be 2 spaces (from .editorconfig)
5. Save: Should use Prettier with project config

The formatters should just work! If they don't, the issue is likely in your LazyVim config (which I'm not touching).
