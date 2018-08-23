# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD
### Added
- Modules that extend `AbstractBaseMap` may now get injected with a config map.
- Modules that extend `AbstractBaseMap` no longer need to implement a constructor equivalent for `_initModule()`.

### Changed
- Modules that extend `AbstractBaseMap` now receive their key and dependencies in the new config map.
- Modules that extend `AbstractBaseMap` merge the injected config into the final setup config.

## [0.1-alpha1] - 2018-05-11
Initial version.
