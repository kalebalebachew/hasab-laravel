# Contributing to Hasab AI Laravel SDK

Hey there! Thanks for considering contributing to this project. 

## Getting Started

1. **Fork the repo** on GitHub
2. **Clone your fork** locally
3. **Install dependencies**:
   ```bash
   composer install
   ```

That's the setup. Easy.

## Before You Code

- **Check existing issues** to see if someone's already working on it
- **Open an issue** if you're planning something big (new features, breaking changes, etc.)
- **Keep it simple** – this package is intentionally small and focused

## Code Standards

We follow Laravel conventions and PSR-12. Here's what that means in practice:

### PHP Style

```php
// Good
public function transcribe(array $options): array
{
    $response = $this->client->post('transcribe', $options);

    return $response->json();
}

// Bad
public function transcribe($options) {
  $response=$this->client->post('transcribe',$options);
  return $response->json();
}
```

### Class Structure

- Type hint everything (params, returns, properties)
- Keep methods focused and single-purpose
- Favor composition over inheritance

### Naming Conventions

- **Classes**: `PascalCase` (e.g., `ChatService`, `HasabClient`)
- **Methods**: `camelCase` (e.g., `synthesize()`, `getHistory()`)
- **Variables**: `camelCase` (e.g., `$audioFile`, `$apiResponse`)
- **Constants**: `SCREAMING_SNAKE_CASE` (e.g., `DEFAULT_MODEL`)

## What to Contribute

### Bugs

Found something broken? Perfect.

1. Check if there's already an issue for it
2. If not, open one with:
   - What you expected to happen
   - What actually happened
   - Steps to reproduce
   - Laravel version, PHP version

Then feel free to fix it and submit a PR.

### New Features

Got an idea? Great! But let's chat about it first.

1. Open an issue describing the feature
2. Wait for feedback (usually quick)
3. Once approved, build it and submit a PR

**Note**: This package aims to stay focused on the core Hasab AI services. If your feature is super specific to your use case, it might be better as a separate package or in your app code.

### Documentation

Documentation improvements are always welcome:

- Found a typo? Fix it.
- Example code unclear? Improve it.
- Missing use case? Add it.

Just update the relevant `.md` file and submit a PR.

## PR Guidelines

**Good PRs:**

- Solve one problem
- Include clear description
- Follow existing code style
- Are easy to review (not 1000+ lines)

**Not-so-good PRs:**

- Change multiple unrelated things
- Have vague descriptions like "updates"
- Break existing functionality
- Introduce unnecessary dependencies

## Testing

We don't have automated tests yet (contributions welcome!), but please:

1. **Test manually** with a real Laravel app
2. **Try different scenarios** (success, failure, edge cases)
3. **Check with different PHP/Laravel versions** if you can

If you want to add proper tests (PHPUnit, Pest, whatever), that would be amazing.

## Code Review Process

1. I will review your PR
2. They might request changes don't take it personally lol.
3. Make the requested changes
4. Once approved, it gets merged
5. You become a contributor 

## Need Help?

Stuck? Confused? Not sure if your idea fits?

- Open an issue and ask
- Email me: kalebalebachew4@gmail.com
- Start a discussion on GitHub

I'm here to help. This is a friendly project.

