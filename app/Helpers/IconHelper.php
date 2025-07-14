<?php

namespace App\Helpers;

class IconHelper
{
    protected static $iconMap = [
        // Heroicons mapping to emojis/unicode
        'heroicon-o-star' => '⭐',
        'heroicon-o-heart' => '❤️',
        'heroicon-o-building-office' => '🏢',
        'heroicon-o-building-office-2' => '🏢',
        'heroicon-o-users' => '👥',
        'heroicon-o-user' => '👤',
        'heroicon-o-wrench-screwdriver' => '🔧',
        'heroicon-o-chart-bar' => '📊',
        'heroicon-o-cube' => '📦',
        'heroicon-o-photo' => '📸',
        'heroicon-o-document-text' => '📄',
        'heroicon-o-folder' => '📁',
        'heroicon-o-question-mark-circle' => '❓',
        'heroicon-o-cog' => '⚙️',
        'heroicon-o-envelope' => '✉️',
        'heroicon-o-phone' => '📞',
        'heroicon-o-bars-3' => '☰',
        'heroicon-o-shield-check' => '🛡️',
        'heroicon-o-tag' => '🏷️',
        'heroicon-o-check-circle' => '✅',
        'heroicon-o-x-circle' => '❌',
        'heroicon-o-globe-alt' => '🌍',
        'heroicon-o-eye' => '👁️',
        'heroicon-o-check' => '✓',
        'heroicon-o-archive-box' => '📦',
        // Common icons
        'construction' => '🏗️',
        'target' => '🎯',
        'lightbulb' => '💡',
        'trophy' => '🏆',
        'handshake' => '🤝',
        'clock' => '⏰',
        'rocket' => '🚀',
        'diamond' => '💎',
        'fire' => '🔥',
        'thumbs-up' => '👍',
        'medal' => '🏅',
        'gear' => '⚙️',
        'house' => '🏠',
        'hammer' => '🔨',
        'tools' => '🛠️',
        'chart' => '📈',
        'growth' => '📈',
        'quality' => '✨',
        'innovation' => '💡',
        'experience' => '🎯',
        'professional' => '👔',
        'service' => '🔧',
        'support' => '🤝',
        'trust' => '🤝',
        'reliability' => '⚡',
        'excellence' => '🌟',
    ];

    public function render($iconString)
    {
        if (empty($iconString)) {
            return '⭐'; // Default icon
        }

        // Clean the icon string
        $iconString = trim($iconString);
        
        // Check if it's already an emoji/unicode
        if (preg_match('/[\x{1F300}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $iconString)) {
            return $iconString;
        }

        // Check our mapping
        if (isset(self::$iconMap[$iconString])) {
            return self::$iconMap[$iconString];
        }

        // Try to extract icon name from heroicon format
        if (strpos($iconString, 'heroicon-') === 0) {
            $iconName = str_replace(['heroicon-o-', 'heroicon-m-', 'heroicon-s-'], '', $iconString);
            $iconName = str_replace('-', '_', $iconName);
            
            if (isset(self::$iconMap[$iconName])) {
                return self::$iconMap[$iconName];
            }
        }

        // Return a default icon if not found
        return '⭐';
    }

    public static function get($iconString)
    {
        $instance = new self();
        return $instance->render($iconString);
    }
} 