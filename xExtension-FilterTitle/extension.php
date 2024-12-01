<?php
class FilterTitleExtension extends Minz_Extension {
    public function init(): void {
        $this->registerTranslates();
        $this->registerHook('entry_before_insert', [$this, 'filterEntry']);
    }

    public function handleConfigureAction(): void {
        $this->registerTranslates();
        if (Minz_Request::isPost()) {
            $blacklist = array_filter(Minz_Request::paramTextToArray('blacklist', []));
            $whitelist = array_filter(Minz_Request::paramTextToArray('whitelist', []));
            $markAsRead = Minz_Request::paramString('mark_as_read') === '1' ? '1' : '0';

            // Validate configuration
            if (!$this->isValidConfiguration($blacklist, $whitelist, $markAsRead)) {
                Minz_Log::error('FilterTitleExtension: Invalid configuration');
                return;
            }

            $configuration = [
                'blacklist' => $blacklist,
                'mark_as_read' => $markAsRead,
                'whitelist' => $whitelist,
            ];

            $this->setSystemConfiguration($configuration);
            Minz_Log::debug('FilterTitleExtension: Configuration saved: ' . json_encode($configuration));
        }
    }

    public function filterEntry($entry) {
        if (!is_object($entry)) {
            return $entry;
        }

        static $configCache = [];
        $configCache['blacklist'] = $configCache['blacklist'] ?? $this->getSystemConfigurationValue('blacklist') ?? [];
        $configCache['whitelist'] = $configCache['whitelist'] ?? $this->getSystemConfigurationValue('whitelist') ?? [];
        $configCache['mark_as_read'] = $configCache['mark_as_read'] ?? $this->getSystemConfigurationValue('mark_as_read');

        if ($this->processPatterns($entry, $configCache['blacklist'], $configCache['mark_as_read'], 'blacklist') === null) {
            return null;
        }

        if ($this->processPatterns($entry, $configCache['whitelist'], $configCache['mark_as_read'], 'whitelist', true) === null) {
            return null;
        }

        return $entry;
    }

    private function processPatterns($entry, array $patterns, string $markAsRead, string $listType, bool $reverseCheck = false): ?object {
        foreach ($patterns as $pattern) {
            if ($this->isPatternFound($entry->title(), $pattern)) {
                if ($reverseCheck) {
                    return $entry;
                }
                Minz_Log::warning(_t(
                    $listType === 'blacklist'
                        ? 'ext.filter_title.warning.blacklist_keyword'
                        : 'ext.filter_title.warning.whitelist_keyword',
                    $entry->title()
                ));
                return null;
            } elseif (!$reverseCheck) {
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

    private function isValidConfiguration(array $blacklist, array $whitelist, string $markAsRead): bool {
        // Add validation logic here if needed
        return true;
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
