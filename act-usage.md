# Running GitHub Actions Locally with Act

## Installation
✅ Act is installed via Homebrew: `brew install act`

## Configuration Files
- `.actrc` - Act configuration with platform mappings and settings
- `.secrets` - Local secrets file (gitignored)

## Common Commands

### List all available workflows and jobs:
```bash
act --list
```

### Run specific jobs:
```bash
# Dry run (shows what would happen without executing)
act -j build-phar --dryrun

# Run the build-phar job (builds PHAR with PHP 8.1)
act -j build-phar

# Run the validate-phar job (requires artifacts from build-phar)
# This will validate against all PHP versions: 8.1, 8.2, 8.3, 8.4
act -j validate-phar
```

### Run workflows by event:
```bash
# Simulate push event
act push

# Simulate pull request event
act pull_request

# Simulate workflow dispatch
act workflow_dispatch
```

## Useful Options

- `--dryrun` - Show what would run without executing
- `--verbose` - Enable verbose logging
- `--reuse` - Reuse containers for faster subsequent runs
- `--secret-file .secrets` - Use local secrets file
- `--env-file .env` - Load environment variables from file
- `-j <job-name>` - Run specific job only
- `-W <workflow-file>` - Run specific workflow file

## Notes

- Docker is required for act to work
- The first run will be slower as it downloads Docker images
- GPG signing steps will be skipped in local runs (secrets not available)
- Some GitHub-specific features may not work identically locally
