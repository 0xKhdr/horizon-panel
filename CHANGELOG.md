# Changelog

All notable changes to this project will be documented in this file.

The format is based on "Keep a Changelog" and follows Semantic Versioning.

- Link to project plan: see `PLAN.md` for milestones and release goals.
- Date format: YYYY-MM-DD
- Release branching: prepare releases from `main` or a release/* branch.

---

## [Unreleased]

### Added
- 

### Changed
- 

### Fixed
- 

### Security
- 

---

## [1.0.0] - 2025-10-19
### Added
- Project plan updated and finalized to target Laravel 12 and Filament 4. See `PLAN.md` for the full migration checklist, phased roadmap, QA gates, and next actionable tasks.

### Changed
- N/A

### Fixed
- N/A

---

## How we write changelog entries
- Every PR that introduces user-visible changes, infra changes, migrations, or notable bug fixes should include a `CHANGELOG` entry.
- Add entries under the `Unreleased` section in one of the categories: Added, Changed, Deprecated, Removed, Fixed, Security.
- When a release is prepared, move Unreleased entries into a new version section and add the release date.

## Release process (recommended)
1. Ensure all PRs intended for the release are merged into `main` or a `release/x.y` branch.
2. Update `CHANGELOG.md`:
   - Move and consolidate `Unreleased` items under a new header `## [x.y.z] - YYYY-MM-DD`.
   - Add brief, human-readable notes for each item.
3. Tag the release and push tags:

```bash
git tag -a vx.y.z -m "Release vx.y.z"
git push origin --tags
```

4. Create a GitHub Release (optional): paste the changelog notes into the release body.

## Versioning
- We follow Semantic Versioning: MAJOR.MINOR.PATCH.
  - MAJOR for breaking changes (e.g., Laravel major upgrades that break compatibility).
  - MINOR for new features and improvements that retain backward compatibility.
  - PATCH for bug fixes and small changes.

## Commit & PR guidance
- Use Conventional Commits (optional but recommended): `feat:`, `fix:`, `chore:`, `docs:`, `refactor:`, `perf:`, `test:`, `build:`.
- Each PR should reference an issue and include a short `CHANGELOG` note under `Unreleased` when appropriate.

## Automation tips
- Consider adding a CI job that ensures `CHANGELOG.md` has no unresolved `Unreleased` items before creating a release (or validates presence of changelog entries on PRs that change src/ or migrations).
- For larger teams, adopt a small script to append entries into `Unreleased` automatically from PR templates or commit messages.

---

_Last updated: 2025-10-19_
