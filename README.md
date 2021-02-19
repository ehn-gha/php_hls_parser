# HLS parser

## Installation

```sh
composer require ehngha/hls-parser
```

## How to use (in one word)

```php
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Parser;

$dispatcher = new EventDispatcherInterface(); // a symfony event dispatcher implementation
$client = new HttpClientInterface(); // a symfony http client implementation

// initialize the parser in one line
$parser = Parser::buildParser($dispatcher, $client);

// parsing a master playlist
$master = new Master(url: "foo://bar.foo/master.m3u8");

$playlists = $parser->parseMaster($master);
// $playlists contains all media playlists sorted from best to lowest quality

$playlistBest = $playlists->getBestQuality(); // want the best quality
$playlistsLowest = $playlists->getLowestQuality(); // and lowest
$fragmentsBest = $parser->parsePlaylist($playlistBest);
// $fragmentsBest contains all downloadable media fragments for the best quality
$fragmentsLowest = $parser->parsePlaylist($playlistsLowest);
// $fragmentsLowest contains all downloadable media fragments for the lowest quality
```
