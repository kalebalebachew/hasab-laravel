<?php

/**
 * Hasab AI Transcription Service - Usage Examples
 * 
 * This file demonstrates various ways to use the Transcription Service
 * to convert audio files to text with optional translation and summarization.
 */

use Hasab\Facades\Hasab;

// ============================================================================
// Example 1: Basic Transcription (File Upload)
// ============================================================================
// The simplest way - just provide a file path
// The SDK automatically handles required fields like 'key' and 'is_meeting'

try {
    $result = Hasab::transcription()->upload([
        'file' => storage_path('app/audio/recording.mp3'),
    ]);

    echo "Transcription: " . $result['transcription'] . "\n";
    echo "Audio ID: " . $result['audio']['id'] . "\n";
    echo "Duration: " . $result['audio']['duration_in_seconds'] . " seconds\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


// ============================================================================
// Example 2: Transcription from URL
// ============================================================================
// If your audio file is already hosted online, use the URL method

try {
    $result = Hasab::transcription()->upload([
        'url' => 'https://example.com/audio/interview.mp3',
        // Optional: provide a custom key for identification
        'key' => 'interview-2024-jan',
        'is_meeting' => false,
    ]);

    echo "Transcription: " . $result['transcription'] . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


// ============================================================================
// Example 3: Transcription with All Options
// ============================================================================
// Use all available features: transcription, translation, and summarization

try {
    $result = Hasab::transcription()->upload([
        'file' => storage_path('app/audio/presentation.wav'),
        
        // Identification
        'key' => 'sales-presentation-q1',  // Custom identifier
        'is_meeting' => false,             // Not a meeting recording
        
        // Processing options
        'transcribe' => true,              // Get transcription
        'translate' => true,               // Get translation
        'summarize' => true,               // Get summary
        'timestamps' => true,              // Get word-level timestamps
        
        // Language settings
        'source_language' => 'amh',        // Source: Amharic
        'language' => 'eng',               // Target: English
    ]);

    // Access different outputs
    echo "Original Transcription:\n" . $result['transcription'] . "\n\n";
    echo "Translation:\n" . $result['translation'] . "\n\n";
    echo "Summary:\n" . $result['summary'] . "\n\n";
    
    // Process timestamps
    if (!empty($result['timestamp'])) {
        echo "Timestamps:\n";
        foreach ($result['timestamp'] as $segment) {
            printf("[%.2fs - %.2fs]: %s\n", 
                $segment['start'], 
                $segment['end'], 
                $segment['content']
            );
        }
    }
    
    // Token usage
    echo "\nTokens used: " . $result['metadata']['tokens_charged'] . "\n";
    echo "Remaining tokens: " . $result['metadata']['remaining_tokens'] . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


// ============================================================================
// Example 4: Meeting Transcription with Summarization
// ============================================================================
// Special handling for meeting recordings

try {
    $result = Hasab::transcription()->upload([
        'file' => storage_path('app/meetings/team-standup.mp3'),
        'key' => 'standup-' . date('Y-m-d'),
        'is_meeting' => true,              // Mark as meeting
        'transcribe' => true,
        'summarize' => true,               // Get meeting summary
        'timestamps' => true,              // Track speaker timing
    ]);

    // Display meeting summary
    echo "Meeting Summary:\n";
    echo $result['summary'] . "\n\n";
    
    // Full transcript
    echo "Full Transcript:\n";
    echo $result['transcription'] . "\n";
    
    // Save audio ID for later reference
    $audioId = $result['audio']['id'];
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


// ============================================================================
// Example 5: Retrieve Previous Transcription
// ============================================================================
// Get a transcription you processed earlier using its ID

try {
    $audioId = 8769; // ID from previous upload
    $audio = Hasab::transcription()->get($audioId);
    
    echo "Filename: " . $audio['filename'] . "\n";
    echo "Duration: " . $audio['duration_in_seconds'] . " seconds\n";
    echo "Transcription: " . $audio['transcription'] . "\n";
    echo "Audio URL: " . $audio['audio_url'] . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


// ============================================================================
// Example 6: Get Transcription History
// ============================================================================
// List all your previous transcriptions with pagination

try {
    // Get first page
    $history = Hasab::transcription()->history();
    
    echo "Total transcriptions: " . $history['data']['total'] . "\n";
    echo "Current page: " . $history['data']['current_page'] . "\n";
    echo "Per page: " . $history['data']['per_page'] . "\n\n";
    
    // List transcriptions
    foreach ($history['data']['data'] as $item) {
        echo "ID: {$item['id']} - {$item['filename']} ({$item['duration_in_seconds']}s)\n";
    }
    
    // Get page 2
    $page2 = Hasab::transcription()->history(2);
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


// ============================================================================
// Example 7: Delete a Transcription
// ============================================================================
// Remove a transcription from your history

try {
    $audioId = 8769;
    $result = Hasab::transcription()->delete($audioId);
    
    if ($result['success']) {
        echo "Successfully deleted audio #{$audioId}\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


// ============================================================================
// Example 8: Practical Use Case - Laravel Controller
// ============================================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hasab\Facades\Hasab;

class TranscriptionController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a|max:102400', // Max 100MB
        ]);

        // Store the uploaded file
        $path = $request->file('audio')->store('audio', 'local');
        $fullPath = storage_path('app/' . $path);

        try {
            // Transcribe the audio
            $result = Hasab::transcription()->upload([
                'file' => $fullPath,
                'key' => 'user-upload-' . time(),
                'transcribe' => true,
                'summarize' => $request->boolean('summarize', false),
                'source_language' => $request->input('language', 'auto'),
            ]);

            // Optionally delete the local file after successful transcription
            // unlink($fullPath);

            return response()->json([
                'success' => true,
                'transcription' => $result['transcription'],
                'summary' => $result['summary'] ?? null,
                'audio_id' => $result['audio']['id'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $audio = Hasab::transcription()->get($id);
            return response()->json($audio);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Transcription not found'
            ], 404);
        }
    }

    public function index()
    {
        $page = request('page', 1);
        $history = Hasab::transcription()->history($page);
        
        return response()->json($history);
    }

    public function destroy($id)
    {
        try {
            $result = Hasab::transcription()->delete($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete transcription'
            ], 500);
        }
    }
}


// ============================================================================
// Common Error Handling Pattern
// ============================================================================

function safeTranscribe(string $filePath): ?array
{
    try {
        return Hasab::transcription()->upload([
            'file' => $filePath,
        ]);
    } catch (\Illuminate\Http\Client\RequestException $e) {
        // API request failed
        logger()->error('Transcription API error', [
            'message' => $e->getMessage(),
            'file' => $filePath,
        ]);
        return null;
    } catch (\InvalidArgumentException $e) {
        // Invalid parameters (e.g., file not found)
        logger()->error('Invalid transcription parameters', [
            'message' => $e->getMessage(),
            'file' => $filePath,
        ]);
        return null;
    } catch (\Exception $e) {
        // Other errors
        logger()->error('Unexpected transcription error', [
            'message' => $e->getMessage(),
            'file' => $filePath,
        ]);
        return null;
    }
}

