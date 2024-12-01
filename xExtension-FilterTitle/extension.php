<?php

class FilterTitleExtension extends Minz_Extension {
    public function init(): void {
        $this->registerTranslates();
        $this->registerHook('entry_before_insert', [$this, 'filterTitle']);
    }

    public function handleConfigureAction(): void {
        $this->registerTranslates();

        if (Minz_Request::isPost()) {
            $blacklist = array_filter(Minz_Request::paramTextToArray('blacklist', []));
            $whitelist = array_filter(Minz_Request::paramTextToArray('whitelist', []));

            $configuration = [
                'blacklist' => $blacklist,
                'mark_as_read' => Minz_Request::paramString('mark_as_read') === '1' ? '1' : '0',
                'whitelist' => $whitelist,
            ];

            $this->setSystemConfiguration($configuration);

            // Debugging to verify the values
            Minz_Log::debug('FilterTitleExtension: Configuration saved', $configuration);
        }
    }

    public function filterTitle($entry) {
        if (!is_object($entry)) {
            return $entry;
        }

        // Cache configuration values to avoid repeated lookups
        static $configCache = [];
        $configCache['blacklist'] = $configCache['blacklist'] ?? $this->getSystemConfigurationValue('blacklist') ?? [];
        $configCache['whitelist'] = $configCache['whitelist'] ?? $this->getSystemConfigurationValue('whitelist') ?? [];
        $configCache['mark_as_read'] = $configCache['mark_as_read'] ?? $this->getSystemConfigurationValue('mark_as_read');

        // Check blacklist
        if ($this->processPatterns($entry, $configCache['blacklist'], $configCache['mark_as_read'], 'blacklist') === null) {
            return null;
        }

        // Check whitelist
        if ($this->processPatterns($entry, $configCache['whitelist'], $configCache['mark_as_read'], 'whitelist', true) === null) {
            return null;
        }

        return $entry;
    }

    private function processPatterns($entry, array $patterns, string $markAsRead, string $listType, bool $reverseCheck = false) {
        foreach ($patterns as $pattern) {
            $found = self::isPatternFound($entry->title(), $pattern);

            if ($reverseCheck ? !$found : $found) {
                if ($markAsRead === '1') {
                    $entry->_isRead(true);
                    return $entry;
                }

                Minz_Log::warning(_t(
                    $listType === 'blacklist'
                        ? 'ext.filter_title.warning.blacklist_keyword'
                        : 'ext.filter_title.warning.whitelist_keyword',
                    $entry->title()
                ));

                return null;
            }
        }

        return $entry;
    }

    private function isPatternFound(string $title, string $pattern): bool {
        return @preg_match($pattern, $title) === 1 || strpos($title, $pattern) !== false;
    }

    private function isValidRegex(string $pattern): bool {
        return @preg_match($pattern, '') !== false;
    }

    private function getConfigurationData(string $key, string $fallbackKey): string {
        return implode(PHP_EOL, $this->getSystemConfigurationValue($key) ?? $this->getSystemConfigurationValue($fallbackKey) ?? []);
    }

    public function getBlacklistData(): string {
        return $this->getConfigurationData('blacklist', 'blacklist_title_keywords');
    }

    public function getWhitelistData(): string {
        return $this->getConfigurationData('whitelist', 'blacklist_title_keywords');
    }
}
