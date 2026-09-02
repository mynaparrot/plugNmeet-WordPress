# Changelog

## [2.2.7](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.2.6...v2.2.7) (2026-09-02)


### ⚠ BREAKING CHANGES

* new UI + API

### Features

* added option to enable analytics ([2049639](https://github.com/mynaparrot/plugNmeet-WordPress/commit/2049639aaaaf319df2b1789db43e7fa0e73f04ab))
* auto recording + playback recording ([262f21c](https://github.com/mynaparrot/plugNmeet-WordPress/commit/262f21cae2f7a6fc373d1d529f5440f46d27741e))
* Display external link features ([79b99cd](https://github.com/mynaparrot/plugNmeet-WordPress/commit/79b99cd8720805562113f3da9f9a5978bdafbc08))
* Enable End-To-End Encryption (E2EE) ([e2872c9](https://github.com/mynaparrot/plugNmeet-WordPress/commit/e2872c951b59f19553ccb64030007452d1c19533))
* interface customization ([35af9f9](https://github.com/mynaparrot/plugNmeet-WordPress/commit/35af9f9727224ac156ad4e2731c0d391b9c1b8bf))
* load client from remote ([aff9ff9](https://github.com/mynaparrot/plugNmeet-WordPress/commit/aff9ff92f10194a56bc9b87c8a4bb724883ad51f))
* menu to display room artifacts ([6c41de2](https://github.com/mynaparrot/plugNmeet-WordPress/commit/6c41de28f293a620402ec32f7cdc8110e62711d1))
* menu to display room artifacts ([268cad0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/268cad02968128ea6bddf555cd061e96ade08178))
* new UI + API ([5984fac](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5984fac3857c9a840c201e8bc2d6b9d7345c419e))
* option to disable virtualBackgrounds & raiseHand ([273c8b3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/273c8b3b64197ca6fd58884b515c09726f2e4a3b))
* option to set audio preset, default: music e.g Bitrate 32000 ([56acdcc](https://github.com/mynaparrot/plugNmeet-WordPress/commit/56acdcc4974fc22137d69a052d684e0e69c974ff))
* SIP/VoIP dial in ([0d541a9](https://github.com/mynaparrot/plugNmeet-WordPress/commit/0d541a9a6e70c871a8e5b1455e8bc93bbdc576fc))
* speech to text/translation ([749641d](https://github.com/mynaparrot/plugNmeet-WordPress/commit/749641d907f07b2d48865c2882794a46bdb8179f))
* webhook receiver ([25993db](https://github.com/mynaparrot/plugNmeet-WordPress/commit/25993db87d4e4f5bd87899bcb1550e5ce8cad421))


### Bug Fixes

* `client_download_url` was displaying in invalid case ([5e4265e](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5e4265e467e29940699791ccc3796c93b24e96ba))
* add redirect method + fix webhook token ([56367a2](https://github.com/mynaparrot/plugNmeet-WordPress/commit/56367a20fd905f121c3467b55a010084c06d1aeb))
* added cache support ([8f9b717](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8f9b7173be31b36df84ac5221f1f33dbcf65994a))
* added feature text ([7443540](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7443540cb60497ee13ea3f56ead1e962c594a7de))
* added helper method `get_post_param` for easy sanitize POST parameters ([e11c66a](https://github.com/mynaparrot/plugNmeet-WordPress/commit/e11c66a7f91bd4fbeff3b326b8568aea64f70071))
* added missing version number ([41199fc](https://github.com/mynaparrot/plugNmeet-WordPress/commit/41199fc0fc1dc30e808530865880c428c81af759))
* added mo file ([6fc8102](https://github.com/mynaparrot/plugNmeet-WordPress/commit/6fc8102d898863550cf67f749920fb2f0ff74451))
* added more clear messages ([5079e9d](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5079e9d0aa47be14e087492dc29c79bfa5ba855e))
* added new reactions option ([af2a8cd](https://github.com/mynaparrot/plugNmeet-WordPress/commit/af2a8cda022b19dc09d8dfbdbfeb64c0e2196827))
* added setting to assign custom `room_slug_path` value ([ac8f80d](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ac8f80d65e68c37a9e4eb7d6d37e4eb12d94046d))
* added tested upto `7.1` + fix `shared_notepad_status` ([bbf3d28](https://github.com/mynaparrot/plugNmeet-WordPress/commit/bbf3d280a6a471f1a9a2d98e6ec2f4716180d08f))
* adjusted few styling ([a654d13](https://github.com/mynaparrot/plugNmeet-WordPress/commit/a654d13626edf3f871af3d5669e67dacde1389c5))
* adjusted some design ([8344a77](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8344a77c67d2bc68159ae39c1323f85975ce5ed9))
* after delete view wasn't updating ([dc27b46](https://github.com/mynaparrot/plugNmeet-WordPress/commit/dc27b46dda7d5859db3574ed2cff268af0dc7d38))
* after save display latest data ([d352261](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d3522619f89527bcf3ef2db44fb4986209b35628))
* aggressively overwrite the entire script and style queue ([467e1f3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/467e1f3377be8fc04d6758161c3f4f4bd4379ba5))
* allow theme dev to easy override ([b86c506](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b86c506b245965fd15e3436fbdd545de827bdaf2))
* before session starting do more checking ([f0602e0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/f0602e0d28a824c0c645093cb80d2d462d738a0d))
* better use methods directly ([c9c9f09](https://github.com/mynaparrot/plugNmeet-WordPress/commit/c9c9f0953cd4503095bf785b519941f8dd437f0d))
* bump language strings ([93f83aa](https://github.com/mynaparrot/plugNmeet-WordPress/commit/93f83aa8b3d6b8149d4bdc5724a624c820fc83af))
* bump minimum ([e28102b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/e28102b50dd34365c88a10fa32d4d41795066f64))
* bump PHP requirement to `8.3.0` ([f24d98e](https://github.com/mynaparrot/plugNmeet-WordPress/commit/f24d98e15a79f6f62e9fcaf7a8000852428933df))
* bump SDK ([7d4927a](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7d4927ac8f60aaa214f7bc381e4d2a5983296716))
* bump SDK ([1348499](https://github.com/mynaparrot/plugNmeet-WordPress/commit/13484994b42850c3c3a834af3c1dedd331005efe))
* bump SDK ([1576c74](https://github.com/mynaparrot/plugNmeet-WordPress/commit/1576c74279a60fd133d574efdf257199b481ebaf))
* bump SDK ([051025b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/051025b700bdc6adef9f44aa931af74351a16e3d))
* bump SDK ([30dd2a9](https://github.com/mynaparrot/plugNmeet-WordPress/commit/30dd2a912ee921b3729201d0579ad3d66f201bcc))
* bump SDK for proper duration calculation bug fix ([cba2d8e](https://github.com/mynaparrot/plugNmeet-WordPress/commit/cba2d8e9a93dbd29da8702c64aa29d3b5c6719d7))
* **bump:** bump version ([1104dff](https://github.com/mynaparrot/plugNmeet-WordPress/commit/1104dffad3113e8e01ebfdda458a07e1e85dfaa0))
* cache API results and clean from webhook ([ce498b6](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ce498b68c79a91d28760c640633aa6721387e853))
* **ci:** release-please-action added ([d9ef8b0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d9ef8b0929166a44833a4967cdaaff1ca1d3bdc8))
* **ci:** small fix ([eca922c](https://github.com/mynaparrot/plugNmeet-WordPress/commit/eca922ccde3f55bf2ae72ae9288284a6e774943a))
* **ci:** small fix ([2583b8a](https://github.com/mynaparrot/plugNmeet-WordPress/commit/2583b8a08679cb1485b61d3ac82bdae93b431c2b))
* **ci:** small fix ([d08271f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d08271faa68e6bc1fda908decb3abdd9e272fb4d))
* clean up code ([7bd09f4](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7bd09f4a7c33a2d4fb34e3029cbeb0d499d8fdc1))
* clean up code + potentially fixed: https://github.com/mynaparrot/plugNmeet-WordPress/issues/45 ([db5fd9b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/db5fd9b2ceb249d9336fb6414103c409982c8b43))
* cleanup ([5f46cb1](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5f46cb17be939a12e04e09282d878405a79cc57c))
* cleanup code and remove confusing method name ([b799d9b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b799d9b790b6f7b79488842c781b618a214933c5))
* **cnf:** Crowdin ([a4183f2](https://github.com/mynaparrot/plugNmeet-WordPress/commit/a4183f26358e00357abd4659353f34b0b38adac3))
* critical bug when sanitizing array type using `sanitize_text_field` ([d0a3cfd](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d0a3cfdc51160399f05012def1e696b81c090e57))
* default to `vp9` codec as fallback to `vp8` in firefox ([4a895ab](https://github.com/mynaparrot/plugNmeet-WordPress/commit/4a895ab763aba9194fa44009ae5053ef52d9068f))
* display `shortcode` as well ([badacba](https://github.com/mynaparrot/plugNmeet-WordPress/commit/badacba0e31b0d38d793221e2d96a4404bcef638))
* display number of items + page info ([42a8a82](https://github.com/mynaparrot/plugNmeet-WordPress/commit/42a8a82d497d07ca640580c50c78fd25b4f2902d))
* display number of items + page info ([b3b19e6](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b3b19e6406f7f183ac5529817eb8c5533a7b2499))
* display proper notification ([8707c7c](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8707c7c2791c870c17c7d25edd6fcdb84de708b9))
* display room table ID as well ([7dd47fa](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7dd47fa88e3988db72b5f3b18bb14fbe91a47eba))
* **doc:** update link ([32ae30f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/32ae30f92e6958ac66b1e532b27545ce70b5d5cc))
* duplicate `type` was adding ([d4869dc](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d4869dc085d175799ac6bd7e94628f318ec6e7c0))
* ensure multiple rooms data can display in same page + clean up ([b3b34d0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b3b34d078993e170c657b30a7828bdd80823683c))
* **feat:** Implement robust frontend room access via clean URLs ([ba6aec5](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ba6aec55631accfc871520a00a5488f52801c2c9))
* **feat:** SDK update + added merge recording feature ([a6de3eb](https://github.com/mynaparrot/plugNmeet-WordPress/commit/a6de3ebd24c6d89c3cf8c6ae0b6f4e015a95c49e))
* generate mo file if change happen ([27154fe](https://github.com/mynaparrot/plugNmeet-WordPress/commit/27154fe26ac87d438408a20e5052f67a99c77fb0))
* mentioned regarding demo server ([7d5a3ee](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7d5a3eed16ccfe340711deddd1fc3f25503ed824))
* new Crowdin updates ([9bc64a3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/9bc64a3fae98fa2cc2170c1508d3efb7c3f3f456))
* new Crowdin updates ([f9e5132](https://github.com/mynaparrot/plugNmeet-WordPress/commit/f9e51326f409c760881a9cb39887d7f3ee46417a))
* new Crowdin updates ([d75a862](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d75a86276c065224ccaaf400758054b57c659bde))
* new Crowdin updates ([ccef098](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ccef0986de880ac2d7cbe49ebde2d4f4ffc1bdaf))
* new Crowdin updates ([87731a3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/87731a31d0be06fa129a32ddc213470ae7ef70eb))
* new Crowdin updates ([d838fc0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d838fc0615dc598ed53d35252e362cb14c4fc3af))
* new Crowdin updates ([4c50164](https://github.com/mynaparrot/plugNmeet-WordPress/commit/4c50164123f76a4b16f7e0794935851c47b0d4ec))
* new Crowdin updates ([ec09006](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ec09006f5939c38b9f28f562dceef528826039f1))
* new Crowdin updates ([#29](https://github.com/mynaparrot/plugNmeet-WordPress/issues/29)) ([67acc97](https://github.com/mynaparrot/plugNmeet-WordPress/commit/67acc97d3c4456fc83ba6cad6a6ed233b607660f))
* new Crowdin updates ([#30](https://github.com/mynaparrot/plugNmeet-WordPress/issues/30)) ([77f47d0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/77f47d0630aa5ff08cc0985cbcc4827e2ed3e154))
* new Crowdin updates ([#32](https://github.com/mynaparrot/plugNmeet-WordPress/issues/32)) ([aa9c8e3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/aa9c8e3bd6ac5c095f2e699178fddf1dd9c6c217))
* new Crowdin updates ([#33](https://github.com/mynaparrot/plugNmeet-WordPress/issues/33)) ([13ea4ab](https://github.com/mynaparrot/plugNmeet-WordPress/commit/13ea4abfdb55569dc78ff6851827bf9c4270cc8d))
* new Crowdin updates ([#74](https://github.com/mynaparrot/plugNmeet-WordPress/issues/74)) ([782be08](https://github.com/mynaparrot/plugNmeet-WordPress/commit/782be08cccc0f5c0825d858517b91fa887c30a05))
* pass `searchParams` to class ([245026d](https://github.com/mynaparrot/plugNmeet-WordPress/commit/245026d13da20138ece3e78ac839493e24e28adb))
* popup player was using invalid size ([7608b85](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7608b85d14a7458aa9288f10ac7c8795446e9960))
* **refactor:** refactored the way public view and removed out custom CSS to use standard classes ([0f2a67f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/0f2a67fc5ea6242e81fb367579a8f71b250a3c1a))
* remember last working tab ([6a19627](https://github.com/mynaparrot/plugNmeet-WordPress/commit/6a196279533f63e9f5dea4471736c59fc57e7f0b))
* remove 'v' prefix ([8175c3f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8175c3f89e44e95c89a8ac191fca45b7098a97d8))
* remove custom design ([5db86e8](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5db86e829bfa833a2f7c726e4b3472b7256c5af5))
* replaced `<p>` tags with `<div>` ([cef7092](https://github.com/mynaparrot/plugNmeet-WordPress/commit/cef7092dc3be91919ac4eb8e466b913e5adfcfb0))
* replaced bootstrap with native wp style ([32447b6](https://github.com/mynaparrot/plugNmeet-WordPress/commit/32447b6f43d82b58603b9ed846155ea3c9903945))
* replaced by wp color picker ([14089b0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/14089b0661df82345f2ed765ba1cee7da56642fd))
* replaced with new `shared_notepad_status` ([bfe8800](https://github.com/mynaparrot/plugNmeet-WordPress/commit/bfe880082694537f918035e79c6f6d2dad97c9cf))
* restore last page data when back ([033a47a](https://github.com/mynaparrot/plugNmeet-WordPress/commit/033a47a673e77250e2196d20afa50124b55e792c))
* table wasn't change ([36042ac](https://github.com/mynaparrot/plugNmeet-WordPress/commit/36042ac8d0256bca21ff96fe2785dbb8f89c9371))
* update deps ([1d5e2e4](https://github.com/mynaparrot/plugNmeet-WordPress/commit/1d5e2e417e875b961929d41f3b99c48bfd63d69c))
* update doc and preparing for new version ([3f11132](https://github.com/mynaparrot/plugNmeet-WordPress/commit/3f111323a319ff2ceeb9c44608a2a8ec2e4f5bb8))
* update lang ([cdfea5f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/cdfea5f8b3b074938b956b748b4e9253c9e04d6c))
* update language file ([e9a46f4](https://github.com/mynaparrot/plugNmeet-WordPress/commit/e9a46f4a31a075750186b1e90671b1229190e8c8))
* use `toFixed(2)` for recording file size ([8e39e7c](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8e39e7cef8ac8d89a2f844b5b221d88bf4da8134))
* use `wp_kses` like other methods ([aa76a36](https://github.com/mynaparrot/plugNmeet-WordPress/commit/aa76a36d4642c06094a6c75ef7b47d473a08d49a))
* use html `code` tag ([ba8e4cc](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ba8e4cc01d5bac96569650db7b6ec6d71d9a0412))
* use text type ([91bd8a5](https://github.com/mynaparrot/plugNmeet-WordPress/commit/91bd8a59217b68fe7913c90945bb2601b46a2f2b))
* use wp constants ([c5c8d4f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/c5c8d4fe2fe1ebcc0c0fb168e44d8dfd4a7827d2))
* verify response + data ([0ecec5b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/0ecec5b90f9bbaf04b21e2fcfcc5cc4e649c201d))

## [2.2.6](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.2.5...v2.2.6) (2026-08-12)


### Bug Fixes

* bump SDK ([1576c74](https://github.com/mynaparrot/plugNmeet-WordPress/commit/1576c74279a60fd133d574efdf257199b481ebaf))
* bump SDK for proper duration calculation bug fix ([cba2d8e](https://github.com/mynaparrot/plugNmeet-WordPress/commit/cba2d8e9a93dbd29da8702c64aa29d3b5c6719d7))

## [2.2.5](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.2.4...v2.2.5) (2026-07-10)


### Bug Fixes

* added new reactions option ([af2a8cd](https://github.com/mynaparrot/plugNmeet-WordPress/commit/af2a8cda022b19dc09d8dfbdbfeb64c0e2196827))
* default to `vp9` codec as fallback to `vp8` in firefox ([4a895ab](https://github.com/mynaparrot/plugNmeet-WordPress/commit/4a895ab763aba9194fa44009ae5053ef52d9068f))
* new Crowdin updates ([#74](https://github.com/mynaparrot/plugNmeet-WordPress/issues/74)) ([782be08](https://github.com/mynaparrot/plugNmeet-WordPress/commit/782be08cccc0f5c0825d858517b91fa887c30a05))

## [2.2.4](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.2.3...v2.2.4) (2026-06-15)


### Bug Fixes

* `client_download_url` was displaying in invalid case ([5e4265e](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5e4265e467e29940699791ccc3796c93b24e96ba))
* after delete view wasn't updating ([dc27b46](https://github.com/mynaparrot/plugNmeet-WordPress/commit/dc27b46dda7d5859db3574ed2cff268af0dc7d38))
* new Crowdin updates ([f9e5132](https://github.com/mynaparrot/plugNmeet-WordPress/commit/f9e51326f409c760881a9cb39887d7f3ee46417a))
* update deps ([1d5e2e4](https://github.com/mynaparrot/plugNmeet-WordPress/commit/1d5e2e417e875b961929d41f3b99c48bfd63d69c))
* update lang ([cdfea5f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/cdfea5f8b3b074938b956b748b4e9253c9e04d6c))
* use text type ([91bd8a5](https://github.com/mynaparrot/plugNmeet-WordPress/commit/91bd8a59217b68fe7913c90945bb2601b46a2f2b))
* verify response + data ([0ecec5b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/0ecec5b90f9bbaf04b21e2fcfcc5cc4e649c201d))

## [2.2.3](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.2.2...v2.2.3) (2026-06-08)


### Bug Fixes

* critical bug when sanitizing array type using `sanitize_text_field` ([d0a3cfd](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d0a3cfdc51160399f05012def1e696b81c090e57))
* replaced bootstrap with native wp style ([32447b6](https://github.com/mynaparrot/plugNmeet-WordPress/commit/32447b6f43d82b58603b9ed846155ea3c9903945))

## [2.2.2](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.2.1...v2.2.2) (2026-06-06)


### Bug Fixes

* added helper method `get_post_param` for easy sanitize POST parameters ([e11c66a](https://github.com/mynaparrot/plugNmeet-WordPress/commit/e11c66a7f91bd4fbeff3b326b8568aea64f70071))
* added more clear messages ([5079e9d](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5079e9d0aa47be14e087492dc29c79bfa5ba855e))
* added setting to assign custom `room_slug_path` value ([ac8f80d](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ac8f80d65e68c37a9e4eb7d6d37e4eb12d94046d))
* adjusted few styling ([a654d13](https://github.com/mynaparrot/plugNmeet-WordPress/commit/a654d13626edf3f871af3d5669e67dacde1389c5))
* allow theme dev to easy override ([b86c506](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b86c506b245965fd15e3436fbdd545de827bdaf2))
* cleanup code and remove confusing method name ([b799d9b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b799d9b790b6f7b79488842c781b618a214933c5))
* display `shortcode` as well ([badacba](https://github.com/mynaparrot/plugNmeet-WordPress/commit/badacba0e31b0d38d793221e2d96a4404bcef638))
* **feat:** Implement robust frontend room access via clean URLs ([ba6aec5](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ba6aec55631accfc871520a00a5488f52801c2c9))
* new Crowdin updates ([d75a862](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d75a86276c065224ccaaf400758054b57c659bde))
* remove 'v' prefix ([8175c3f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8175c3f89e44e95c89a8ac191fca45b7098a97d8))
* remove custom design ([5db86e8](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5db86e829bfa833a2f7c726e4b3472b7256c5af5))
* replaced `<p>` tags with `<div>` ([cef7092](https://github.com/mynaparrot/plugNmeet-WordPress/commit/cef7092dc3be91919ac4eb8e466b913e5adfcfb0))
* use html `code` tag ([ba8e4cc](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ba8e4cc01d5bac96569650db7b6ec6d71d9a0412))

## [2.2.1](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.2.0...v2.2.1) (2026-06-04)


### Bug Fixes

* ensure multiple rooms data can display in same page + clean up ([b3b34d0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b3b34d078993e170c657b30a7828bdd80823683c))
* pass `searchParams` to class ([245026d](https://github.com/mynaparrot/plugNmeet-WordPress/commit/245026d13da20138ece3e78ac839493e24e28adb))
* popup player was using invalid size ([7608b85](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7608b85d14a7458aa9288f10ac7c8795446e9960))

## [2.2.0](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.1.1...v2.2.0) (2026-06-04)


### Features

* menu to display room artifacts ([6c41de2](https://github.com/mynaparrot/plugNmeet-WordPress/commit/6c41de28f293a620402ec32f7cdc8110e62711d1))
* menu to display room artifacts ([268cad0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/268cad02968128ea6bddf555cd061e96ade08178))
* webhook receiver ([25993db](https://github.com/mynaparrot/plugNmeet-WordPress/commit/25993db87d4e4f5bd87899bcb1550e5ce8cad421))


### Bug Fixes

* add redirect method + fix webhook token ([56367a2](https://github.com/mynaparrot/plugNmeet-WordPress/commit/56367a20fd905f121c3467b55a010084c06d1aeb))
* added cache support ([8f9b717](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8f9b7173be31b36df84ac5221f1f33dbcf65994a))
* added missing version number ([41199fc](https://github.com/mynaparrot/plugNmeet-WordPress/commit/41199fc0fc1dc30e808530865880c428c81af759))
* adjusted some design ([8344a77](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8344a77c67d2bc68159ae39c1323f85975ce5ed9))
* after save display latest data ([d352261](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d3522619f89527bcf3ef2db44fb4986209b35628))
* bump language strings ([93f83aa](https://github.com/mynaparrot/plugNmeet-WordPress/commit/93f83aa8b3d6b8149d4bdc5724a624c820fc83af))
* bump PHP requirement to `8.3.0` ([f24d98e](https://github.com/mynaparrot/plugNmeet-WordPress/commit/f24d98e15a79f6f62e9fcaf7a8000852428933df))
* cache API results and clean from webhook ([ce498b6](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ce498b68c79a91d28760c640633aa6721387e853))
* display number of items + page info ([42a8a82](https://github.com/mynaparrot/plugNmeet-WordPress/commit/42a8a82d497d07ca640580c50c78fd25b4f2902d))
* display number of items + page info ([b3b19e6](https://github.com/mynaparrot/plugNmeet-WordPress/commit/b3b19e6406f7f183ac5529817eb8c5533a7b2499))
* display proper notification ([8707c7c](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8707c7c2791c870c17c7d25edd6fcdb84de708b9))
* display room table ID as well ([7dd47fa](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7dd47fa88e3988db72b5f3b18bb14fbe91a47eba))
* **feat:** SDK update + added merge recording feature ([a6de3eb](https://github.com/mynaparrot/plugNmeet-WordPress/commit/a6de3ebd24c6d89c3cf8c6ae0b6f4e015a95c49e))
* new Crowdin updates ([ccef098](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ccef0986de880ac2d7cbe49ebde2d4f4ffc1bdaf))
* **refactor:** refactored the way public view and removed out custom CSS to use standard classes ([0f2a67f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/0f2a67fc5ea6242e81fb367579a8f71b250a3c1a))
* remember last working tab ([6a19627](https://github.com/mynaparrot/plugNmeet-WordPress/commit/6a196279533f63e9f5dea4471736c59fc57e7f0b))
* replaced by wp color picker ([14089b0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/14089b0661df82345f2ed765ba1cee7da56642fd))
* restore last page data when back ([033a47a](https://github.com/mynaparrot/plugNmeet-WordPress/commit/033a47a673e77250e2196d20afa50124b55e792c))
* use `wp_kses` like other methods ([aa76a36](https://github.com/mynaparrot/plugNmeet-WordPress/commit/aa76a36d4642c06094a6c75ef7b47d473a08d49a))
* use wp constants ([c5c8d4f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/c5c8d4fe2fe1ebcc0c0fb168e44d8dfd4a7827d2))

## [2.1.1](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.1.0...v2.1.1) (2026-02-02)


### Bug Fixes

* bump minimum ([e28102b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/e28102b50dd34365c88a10fa32d4d41795066f64))
* new Crowdin updates ([87731a3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/87731a31d0be06fa129a32ddc213470ae7ef70eb))

## [2.1.0](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.0.2...v2.1.0) (2026-01-22)


### Features

* SIP/VoIP dial in ([0d541a9](https://github.com/mynaparrot/plugNmeet-WordPress/commit/0d541a9a6e70c871a8e5b1455e8bc93bbdc576fc))


### Bug Fixes

* added feature text ([7443540](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7443540cb60497ee13ea3f56ead1e962c594a7de))
* new Crowdin updates ([d838fc0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d838fc0615dc598ed53d35252e362cb14c4fc3af))

## [2.0.2](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.0.1...v2.0.2) (2026-01-13)


### Bug Fixes

* use `toFixed(2)` for recording file size ([8e39e7c](https://github.com/mynaparrot/plugNmeet-WordPress/commit/8e39e7cef8ac8d89a2f844b5b221d88bf4da8134))

## [2.0.1](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.0.0...v2.0.1) (2026-01-09)


### Bug Fixes

* bump SDK ([051025b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/051025b700bdc6adef9f44aa931af74351a16e3d))
* clean up code ([7bd09f4](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7bd09f4a7c33a2d4fb34e3029cbeb0d499d8fdc1))
* duplicate `type` was adding ([d4869dc](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d4869dc085d175799ac6bd7e94628f318ec6e7c0))

## [2.0.0](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v2.0.0...v2.0.0) (2025-12-20)

### ⚠ BREAKING CHANGES

* new UI + API

### Features

* new UI + API ([5984fac](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5984fac3857c9a840c201e8bc2d6b9d7345c419e))


### Bug Fixes

* bump SDK ([30dd2a9](https://github.com/mynaparrot/plugNmeet-WordPress/commit/30dd2a912ee921b3729201d0579ad3d66f201bcc))
* new Crowdin updates ([ec09006](https://github.com/mynaparrot/plugNmeet-WordPress/commit/ec09006f5939c38b9f28f562dceef528826039f1))

## [1.2.17](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v1.2.16...v1.2.17) (2025-11-03)

### Bug Fixes

* clean up code + potentially
  fixed: https://github.com/mynaparrot/plugNmeet-WordPress/issues/45 ([db5fd9b](https://github.com/mynaparrot/plugNmeet-WordPress/commit/db5fd9b2ceb249d9336fb6414103c409982c8b43))

## [1.2.16](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v1.2.15...v1.2.16) (2025-09-23)

### Bug Fixes

* better use methods
  directly ([c9c9f09](https://github.com/mynaparrot/plugNmeet-WordPress/commit/c9c9f0953cd4503095bf785b519941f8dd437f0d))

## [1.2.15](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v1.2.14...v1.2.15) (2025-09-23)

### Bug Fixes

* aggressively overwrite the entire script and style
  queue ([467e1f3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/467e1f3377be8fc04d6758161c3f4f4bd4379ba5))
* before session starting do more
  checking ([f0602e0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/f0602e0d28a824c0c645093cb80d2d462d738a0d))
* cleanup ([5f46cb1](https://github.com/mynaparrot/plugNmeet-WordPress/commit/5f46cb17be939a12e04e09282d878405a79cc57c))
* mentioned regarding demo
  server ([7d5a3ee](https://github.com/mynaparrot/plugNmeet-WordPress/commit/7d5a3eed16ccfe340711deddd1fc3f25503ed824))

## [1.2.14](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v1.2.13...v1.2.14) (2025-09-18)

### Bug Fixes

* update doc and preparing for new
  version ([3f11132](https://github.com/mynaparrot/plugNmeet-WordPress/commit/3f111323a319ff2ceeb9c44608a2a8ec2e4f5bb8))

## [1.2.13](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v1.2.12...v1.2.13) (2024-11-23)

### Bug Fixes

* **ci:** small
  fix ([eca922c](https://github.com/mynaparrot/plugNmeet-WordPress/commit/eca922ccde3f55bf2ae72ae9288284a6e774943a))
* **ci:** small
  fix ([2583b8a](https://github.com/mynaparrot/plugNmeet-WordPress/commit/2583b8a08679cb1485b61d3ac82bdae93b431c2b))
* **doc:** update
  link ([32ae30f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/32ae30f92e6958ac66b1e532b27545ce70b5d5cc))

## [1.2.12](https://github.com/mynaparrot/plugNmeet-WordPress/compare/v1.2.11...v1.2.12) (2024-11-09)

### Bug Fixes

* **bump:** bump
  version ([1104dff](https://github.com/mynaparrot/plugNmeet-WordPress/commit/1104dffad3113e8e01ebfdda458a07e1e85dfaa0))
* **ci:** release-please-action
  added ([d9ef8b0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d9ef8b0929166a44833a4967cdaaff1ca1d3bdc8))
* **ci:** small
  fix ([d08271f](https://github.com/mynaparrot/plugNmeet-WordPress/commit/d08271faa68e6bc1fda908decb3abdd9e272fb4d))
* **cnf:**
  Crowdin ([a4183f2](https://github.com/mynaparrot/plugNmeet-WordPress/commit/a4183f26358e00357abd4659353f34b0b38adac3))
* new Crowdin
  updates ([#29](https://github.com/mynaparrot/plugNmeet-WordPress/issues/29)) ([67acc97](https://github.com/mynaparrot/plugNmeet-WordPress/commit/67acc97d3c4456fc83ba6cad6a6ed233b607660f))
* new Crowdin
  updates ([#30](https://github.com/mynaparrot/plugNmeet-WordPress/issues/30)) ([77f47d0](https://github.com/mynaparrot/plugNmeet-WordPress/commit/77f47d0630aa5ff08cc0985cbcc4827e2ed3e154))
* new Crowdin
  updates ([#32](https://github.com/mynaparrot/plugNmeet-WordPress/issues/32)) ([aa9c8e3](https://github.com/mynaparrot/plugNmeet-WordPress/commit/aa9c8e3bd6ac5c095f2e699178fddf1dd9c6c217))
* new Crowdin
  updates ([#33](https://github.com/mynaparrot/plugNmeet-WordPress/issues/33)) ([13ea4ab](https://github.com/mynaparrot/plugNmeet-WordPress/commit/13ea4abfdb55569dc78ff6851827bf9c4270cc8d))
