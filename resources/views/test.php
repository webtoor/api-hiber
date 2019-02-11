<?php 

   // Creates the Document.
$dom = new DOMDocument('1.0', 'UTF-8');

// Creates the root KML element and appends it to the root document.
$node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
$parNode = $dom->appendChild($node);

// Creates a KML Document element and append it to the KML element.
$dnode = $dom->createElement('Document');
$docNode = $parNode->appendChild($dnode);

// Iterates through the MySQL results, creating one Placemark for each row.

  // Creates a Placemark and append it to the Document.

  $node = $dom->createElement('Placemark');
  $placeNode = $docNode->appendChild($node);

  // Create name, and description elements and assigns them the values of the name and address columns from the results.
  $nameNode = $dom->createElement('name', htmlentities('HIBER'));
  $placeNode->appendChild($nameNode);

  //Creates a Polygon element.
  $polygonNode = $dom->createElement('Polygon', htmlentities(''));
  $placeNode->appendChild($polygonNode);

  // Creates a outerBoundaryIs element.
  $outerBoundNode = $dom->createElement('outerBoundaryIs', htmlentities(''));
  $placeNode->appendChild($outerBoundNode);
 
   // Creates a linearRingNode element.
   $linearRingNode = $dom->createElement('LinearRing');
   $placeNode->appendChild($linearRingNode);

  
  // Creates a coordinates element and gives it the value of the lng and lat columns from the results.
foreach ($koordinat as $data) {
  $coorStr[] = $data['longitude'] . ','  . $data['latitude'];
} 
 
$coorNode = $dom->createElement('coordinates', implode(" ", $coorStr));
$linearRingNode->appendChild($coorNode);

$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml');
header('Content-Disposition: attachment; filename="location.kml"');
echo $kmlOutput; 
?>